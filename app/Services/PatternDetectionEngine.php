<?php

/**
 * Dokumentasi file: Service business logic.
 *
 * Membandingkan check-in sebelum event kehidupan dengan hari lain untuk menemukan pola yang cukup kuat menjadi insight.
 */

namespace App\Services;

use App\Models\DailyCheckin;
use App\Models\InsightPattern;
use App\Models\LifeEvent;
use App\Models\User;
use Carbon\Carbon;

class PatternDetectionEngine
{
    /**
     * Mendeteksi hubungan antara event kehidupan dan perubahan sinyal user.
     *
     * Data event dan check-in dibatasi windowDays, lalu periode 1-5 hari
     * sebelum event dibandingkan dengan hari lain. Engine juga membentuk
     * pasangan tidur-hari berikutnya dan pola vector lain. Insight hanya
     * dibuat jika jumlah sampel minimum dan threshold perubahan terpenuhi,
     * sehingga satu hari anomali tidak langsung dianggap sebagai pola.
     *
     * @return array Insight baru, histori, dan metadata analisis
     */
    public function detectPatterns(User $user, int $windowDays = 30): array
    {
        $startDate = Carbon::today()->subDays($windowDays)->toDateString();

        $events = LifeEvent::where('user_id', $user->id)
            ->where('start_date', '>=', $startDate)
            ->orderBy('start_date', 'desc')
            ->get();

        $checkins = DailyCheckin::with('signal')
            ->where('user_id', $user->id)
            ->where('date', '>=', $startDate)
            ->orderBy('date', 'asc')
            ->get();

        $detectedInsights = [];

        // Bandingkan 1-5 hari sebelum event dengan hari di luar jendela.
        // Minimal 2 check-in pra-event dan 3 check-in pembanding diperlukan
        // agar rata-rata tidak dibangun dari sampel yang terlalu kecil.
        foreach ($events as $event) {
            $eventDate = Carbon::parse($event->start_date);
            $preEventWindowStart = $eventDate->copy()->subDays(5)->toDateString();
            $preEventWindowEnd = $eventDate->copy()->subDays(1)->toDateString();

            $preEventCheckins = $checkins->filter(function ($c) use ($preEventWindowStart, $preEventWindowEnd) {
                return $c->date >= $preEventWindowStart && $c->date <= $preEventWindowEnd;
            });

            $otherCheckins = $checkins->filter(function ($c) use ($preEventWindowStart, $preEventWindowEnd) {
                return $c->date < $preEventWindowStart || $c->date > $preEventWindowEnd;
            });

            if ($preEventCheckins->count() >= 2 && $otherCheckins->count() >= 3) {
                $preAvgSleep = $preEventCheckins->avg(fn ($c) => $c->signal?->sleep_hours ?? 7.0);
                $otherAvgSleep = $otherCheckins->avg(fn ($c) => $c->signal?->sleep_hours ?? 7.0);

                $preAvgStress = $preEventCheckins->avg(fn ($c) => $c->signal?->stress_level ?? 30);
                $otherAvgStress = $otherCheckins->avg(fn ($c) => $c->signal?->stress_level ?? 30);

                $preAvgEnergy = $preEventCheckins->avg(fn ($c) => $c->signal?->energy_level ?? 70);
                $otherAvgEnergy = $otherCheckins->avg(fn ($c) => $c->signal?->energy_level ?? 70);

                // Pola tidur dianggap bermakna jika turun minimal 15% dari
                // baseline hari lain. Rumus ini mengukur proporsi penurunan,
                // bukan selisih jam absolut, agar dapat dibandingkan lintas user.
                if ($otherAvgSleep > 0 && ($otherAvgSleep - $preAvgSleep) / $otherAvgSleep >= 0.15) {
                    $sleepDropPercent = round((($otherAvgSleep - $preAvgSleep) / $otherAvgSleep) * 100);
                    $stressRisePercent = round((($preAvgStress - $otherAvgStress) / max(1, $otherAvgStress)) * 100);

                    $detectedInsights[] = [
                        'type' => 'event_correlation',
                        'event_title' => $event->title,
                        'event_date' => $event->start_date->format('d M Y'),
                        'title' => "Pola Menjelang {$event->title}: Jam Tidur Turun {$sleepDropPercent}%",
                        'summary' => "Setiap mendekati agenda '{$event->title}', jam tidurmu rata-rata berkurang dari ".round($otherAvgSleep, 1).' jam menjadi '.round($preAvgSleep, 1).' jam. Hal ini diikuti penurunan energi ke '.round($preAvgEnergy).'% dan peningkatan stres.',
                        'chain' => [
                            "Mendekati agenda: {$event->title}",
                            "Jam tidur terpangkas (-{$sleepDropPercent}%)",
                            "Tingkat stres & overthinking meningkat (+{$stressRisePercent}%)",
                            'Energi harian drop ke level '.round($preAvgEnergy).'%',
                        ],
                        'recommendation' => "Jadwalkan buffer istirahat ekstra 2 hari sebelum {$event->title} agar staminamu tetap stabil.",
                    ];
                }
            }
        }

        // Bentuk pasangan tidur malam ini dan energi check-in berikutnya.
        // Urutan tanggal dipertahankan agar hubungan waktu tidak tertukar.
        $sleepEnergyPairs = [];
        $checkinsArray = $checkins->values();
        for ($i = 0; $i < $checkinsArray->count() - 1; $i++) {
            $todayC = $checkinsArray[$i];
            $tomorrowC = $checkinsArray[$i + 1];

            if ($todayC->signal && $tomorrowC->signal) {
                $sleepEnergyPairs[] = [
                    'sleep_tonight' => $todayC->signal->sleep_hours,
                    'energy_tomorrow' => $tomorrowC->signal->energy_level,
                ];
            }
        }

        if (count($sleepEnergyPairs) >= 5) {
            $goodSleepDays = array_filter($sleepEnergyPairs, fn ($p) => $p['sleep_tonight'] >= 7.0);
            $badSleepDays = array_filter($sleepEnergyPairs, fn ($p) => $p['sleep_tonight'] < 6.0);

            if (! empty($goodSleepDays) && ! empty($badSleepDays)) {
                $avgEnergyGood = round(array_sum(array_column($goodSleepDays, 'energy_tomorrow')) / count($goodSleepDays));
                $avgEnergyBad = round(array_sum(array_column($badSleepDays, 'energy_tomorrow')) / count($badSleepDays));

                if ($avgEnergyGood - $avgEnergyBad >= 15) {
                    $diff = $avgEnergyGood - $avgEnergyBad;
                    $detectedInsights[] = [
                        'type' => 'sleep_energy_lag',
                        'title' => "Korelasi Nyata: Tidur $\ge$ 7 Jam = Energi Besok Naik +{$diff} Poin",
                        'summary' => "Saat tidur malammu tercukupi $\ge$ 7 jam, energimu keesokan harinya rata-rata mencapai {$avgEnergyGood}%. Sebaliknya saat tidur < 6 jam, energimu turun drastis ke {$avgEnergyBad}%.",
                        'chain' => [
                            "Tidur $\ge$ 7.0 jam di malam hari",
                            'Regenerasi fisik & mental optimal',
                            "Energi esok hari mencapai {$avgEnergyGood}% (+{$diff} poin)",
                            'Fokus belajar/kerja lebih stabil sepanjang hari',
                        ],
                        'recommendation' => 'Prioritaskan jam tidur minimal 7 jam sebagai pondasi energi utamamu.',
                    ];
                }
            }
        }

        // Get saved insights from database
        $savedPatterns = InsightPattern::where('user_id', $user->id)
            ->where('status', 'active')
            ->orderBy('detected_at', 'desc')
            ->get();

        return [
            'events' => $events,
            'detected_insights' => $detectedInsights,
            'saved_patterns' => $savedPatterns,
            'checkins_count' => $checkins->count(),
        ];
    }
}

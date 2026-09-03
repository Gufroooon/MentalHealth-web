<?php

namespace App\Services;

use App\Models\DailyCheckin;
use App\Models\User;
use Carbon\Carbon;

class WhatChangedService
{
    /**
     * Compare current 7-day period (days 0..6) with previous 7-day period (days 7..13)
     */
    public function analyzeWeeklyChanges(User $user): array
    {
        $today = Carbon::today();
        
        // 1. Current 7 Days (Days 0 - 6)
        $currentPeriodCheckins = DailyCheckin::with('signal')
            ->where('user_id', $user->id)
            ->whereBetween('date', [$today->copy()->subDays(6)->toDateString(), $today->toDateString()])
            ->get();

        // 2. Previous 7 Days (Days 7 - 13)
        $previousPeriodCheckins = DailyCheckin::with('signal')
            ->where('user_id', $user->id)
            ->whereBetween('date', [$today->copy()->subDays(13)->toDateString(), $today->copy()->subDays(7)->toDateString()])
            ->get();

        if ($currentPeriodCheckins->isEmpty() || $previousPeriodCheckins->isEmpty()) {
            return [
                'has_comparison' => false,
                'message' => 'NARA membutuhkan setidaknya beberapa hari catatan check-in untuk mulai menganalisis perubahan pola mingguanmu.',
                'changes' => [],
                'alerts' => [],
            ];
        }

        // Calculate averages
        $currStats = $this->calculateAverages($currentPeriodCheckins);
        $prevStats = $this->calculateAverages($previousPeriodCheckins);

        $changes = [];
        $alerts = [];

        $metricsMap = [
            'energy' => ['name' => 'Tingkat Energi', 'unit' => '%', 'higher_is_better' => true],
            'sleep_hours' => ['name' => 'Durasi Tidur', 'unit' => 'jam', 'higher_is_better' => true],
            'sleep_quality' => ['name' => 'Kualitas Tidur', 'unit' => '%', 'higher_is_better' => true],
            'stress' => ['name' => 'Tingkat Stres', 'unit' => '%', 'higher_is_better' => false],
            'overthink' => ['name' => 'Tingkat Overthinking', 'unit' => '%', 'higher_is_better' => false],
            'focus' => ['name' => 'Kemampuan Fokus', 'unit' => '%', 'higher_is_better' => true],
            'workload' => ['name' => 'Beban Kerja / Tugas', 'unit' => '%', 'higher_is_better' => false],
            'social' => ['name' => 'Kualitas Interaksi Sosial', 'unit' => '%', 'higher_is_better' => true],
            'overall' => ['name' => 'Skor Kesejahteraan Keseluruhan', 'unit' => 'poin', 'higher_is_better' => true],
        ];

        foreach ($metricsMap as $key => $meta) {
            $currVal = $currStats[$key] ?? 0;
            $prevVal = $prevStats[$key] ?? 0;

            if ($prevVal == 0) {
                continue;
            }

            $deltaPercent = round((($currVal - $prevVal) / $prevVal) * 100, 1);
            $absDelta = abs($deltaPercent);

            $isSignificant = $absDelta >= 15.0;
            $isPositive = $meta['higher_is_better'] ? ($deltaPercent > 0) : ($deltaPercent < 0);

            $changeItem = [
                'key' => $key,
                'name' => $meta['name'],
                'unit' => $meta['unit'],
                'current_value' => round($currVal, 1),
                'previous_value' => round($prevVal, 1),
                'delta_percent' => $deltaPercent,
                'abs_delta' => $absDelta,
                'is_significant' => $isSignificant,
                'is_positive' => $isPositive,
            ];

            $changes[$key] = $changeItem;

            if ($isSignificant) {
                $direction = $deltaPercent > 0 ? 'meningkat' : 'menurun';
                
                // Construct friendly empathetic insight
                $insight = $this->buildEmpatheticInsight($key, $meta['name'], $deltaPercent, $currVal, $prevVal, $currStats, $prevStats);
                
                $alerts[] = [
                    'key' => $key,
                    'title' => "{$meta['name']} {$direction} {$absDelta}% minggu ini",
                    'delta_percent' => $deltaPercent,
                    'is_positive' => $isPositive,
                    'insight' => $insight,
                    'current' => round($currVal, 1) . ' ' . $meta['unit'],
                    'previous' => round($prevVal, 1) . ' ' . $meta['unit'],
                ];
            }
        }

        return [
            'has_comparison' => true,
            'current_period_count' => $currentPeriodCheckins->count(),
            'previous_period_count' => $previousPeriodCheckins->count(),
            'changes' => $changes,
            'alerts' => $alerts,
            'current_stats' => $currStats,
            'previous_stats' => $prevStats,
        ];
    }

    private function calculateAverages($checkins): array
    {
        $count = $checkins->count();
        if ($count === 0) return [];

        $totals = [
            'overall' => 0,
            'mind' => 0,
            'body' => 0,
            'social' => 0,
            'life' => 0,
            'energy' => 0,
            'sleep_hours' => 0,
            'sleep_quality' => 0,
            'stress' => 0,
            'overthink' => 0,
            'focus' => 0,
            'workload' => 0,
            'social' => 0,
            'loneliness' => 0,
        ];

        foreach ($checkins as $c) {
            $totals['overall'] += $c->overall_wellbeing_score;
            $totals['mind'] += $c->mind_score;
            $totals['body'] += $c->body_score;
            $totals['social'] += $c->social_score;
            $totals['life'] += $c->life_score;

            if ($c->signal) {
                $totals['energy'] += $c->signal->energy_level;
                $totals['sleep_hours'] += $c->signal->sleep_hours;
                $totals['sleep_quality'] += $c->signal->sleep_quality;
                $totals['stress'] += $c->signal->stress_level;
                $totals['overthink'] += $c->signal->overthinking_level;
                $totals['focus'] += $c->signal->focus_level;
                $totals['workload'] += $c->signal->workload_score;
                $totals['social'] += $c->signal->social_interaction_score;
                $totals['loneliness'] += $c->signal->loneliness_score;
            }
        }

        $averages = [];
        foreach ($totals as $k => $v) {
            $averages[$k] = round($v / $count, 1);
        }

        return $averages;
    }

    private function buildEmpatheticInsight(string $key, string $name, float $delta, float $curr, float $prev, array $currStats, array $prevStats): string
    {
        $factors = [];

        if ($key === 'energy' && $delta < 0) {
            if (($currStats['sleep_hours'] ?? 0) < ($prevStats['sleep_hours'] ?? 0)) {
                $factors[] = "Jam tidur (" . ($prevStats['sleep_hours'] ?? 0) . "j → " . ($currStats['sleep_hours'] ?? 0) . "j)";
            }
            if (($currStats['workload'] ?? 0) > ($prevStats['workload'] ?? 0)) {
                $factors[] = "Beban kerja (" . ($prevStats['workload'] ?? 0) . "% → " . ($currStats['workload'] ?? 0) . "%)";
            }
            if (($currStats['stress'] ?? 0) > ($prevStats['stress'] ?? 0)) {
                $factors[] = "Tingkat stres (" . ($prevStats['stress'] ?? 0) . "% → " . ($currStats['stress'] ?? 0) . "%)";
            }
            
            $factorsText = !empty($factors) ? implode(', ', $factors) : "fluktuasi aktivitas harian";
            return "Energimu mengalami penurunan dibanding minggu lalu. Faktor yang mungkin berkontribusi: {$factorsText}. Tubuhmu mungkin sedang meminta ritme yang lebih santai.";
        }

        if ($key === 'stress' && $delta > 0) {
            if (($currStats['workload'] ?? 0) > ($prevStats['workload'] ?? 0)) {
                $factors[] = "Kepadatan tugas/deadline";
            }
            if (($currStats['sleep_hours'] ?? 0) < 6.0) {
                $factors[] = "Kurang jam istirahat";
            }
            $factorsText = !empty($factors) ? implode(' dan ', $factors) : "tuntutan mingguan";
            return "Tingkat stresmu naik {$delta}%. Hal ini mungkin berhubungan dengan {$factorsText}. Jangan lupa luangkan jeda kecil di sela aktivitas.";
        }

        if ($key === 'sleep_hours' && $delta < 0) {
            return "Durasi tidurmu rata-rata berkurang dari {$prev} jam menjadi {$curr} jam. Penurunan jam tidur sering kali berdampak langsung pada kejernihan pikiran dan energi fisik.";
        }

        if ($delta > 0 && in_array($key, ['energy', 'overall', 'sleep_quality', 'focus'])) {
            return "Kabar baik! {$name} menunjukkan peningkatan positif (+{$delta}%). Pola istirahat dan kebiasaanmu minggu ini tampaknya berjalan efektif.";
        }

        return "Terjadi perubahan pada {$name} sebesar " . abs($delta) . "% dibanding periode 7 hari sebelumnya.";
    }
}

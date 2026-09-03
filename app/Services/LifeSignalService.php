<?php

/**
 * Dokumentasi file: Service business logic.
 *
 * Mengubah input check-in menjadi skor Mind, Body, Social, Life, dan Overall lalu menyimpannya bersama detail sinyal.
 */

namespace App\Services;

use App\Models\DailyCheckin;
use App\Models\LifeSignal;
use App\Models\MicroActionLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;

class LifeSignalService
{
    /**
     * Menghitung skor kesejahteraan harian dari data form check-in.
     *
     * Input berasal dari CheckinController dan berisi sinyal rinci tubuh,
     * pikiran, sosial, serta kehidupan. Setiap vector dihitung dengan bobot
     * yang sudah ditentukan, lalu empat vector dirata-ratakan menjadi skor
     * keseluruhan dan diberi primary tag yang paling relevan.
     *
     * DailyCheckin menyimpan skor ringkas untuk dashboard, sedangkan
     * LifeSignal menyimpan metrik mentah agar dapat dipakai analisis pola.
     * Keduanya ditulis dalam satu transaction supaya tidak terjadi kondisi
     * check-in tersimpan tanpa detail sinyal atau sebaliknya.
     */
    public function recordCheckin(User $user, array $data, ?string $date = null): DailyCheckin
    {
        $date = $date ?? Carbon::today()->toDateString();

        // Skor Mind memberi bobot terbesar pada mood dan fokus. Stres serta
        // overthinking dibalik (100 - nilai) karena nilai tinggi pada dua
        // metrik tersebut berarti kondisi pikiran semakin berat.
        $mood = (int) ($data['mood_level'] ?? 70);
        $focus = (int) ($data['focus_level'] ?? 70);
        $stress = (int) ($data['stress_level'] ?? 30);
        $overthink = (int) ($data['overthinking_level'] ?? 30);

        $mindScore = round(
            ($mood * 0.35) +
            ($focus * 0.30) +
            ((100 - $stress) * 0.20) +
            ((100 - $overthink) * 0.15),
            1
        );

        // Skor Body menggabungkan kualitas tidur, energi, aktivitas fisik,
        // dan durasi tidur. Aktivitas dibatasi maksimal 100 agar olahraga
        // yang sangat lama tidak mendominasi vector ini.
        $sleepHours = (float) ($data['sleep_hours'] ?? 7.0);
        $sleepQuality = (int) ($data['sleep_quality'] ?? 70);
        $energy = (int) ($data['energy_level'] ?? 70);
        $activityMin = (int) ($data['physical_activity_min'] ?? 15);

        $sleepDurScore = min(100, ($sleepHours / 8.0) * 100);
        $actScore = min(100, $activityMin * 2.5);

        $bodyScore = round(
            ($sleepQuality * 0.35) +
            ($energy * 0.35) +
            ($actScore * 0.15) +
            ($sleepDurScore * 0.15),
            1
        );

        // Skor Social menganggap interaksi sebagai sinyal utama. Kesepian
        // dan friksi hubungan dibalik karena nilai tinggi menunjukkan beban
        // sosial yang lebih besar, bukan kondisi yang lebih baik.
        $socialInteraction = (int) ($data['social_interaction_score'] ?? 70);
        $loneliness = (int) ($data['loneliness_score'] ?? 20);
        $friction = (int) ($data['relationship_friction_score'] ?? 10);

        $socialScore = round(
            ($socialInteraction * 0.50) +
            ((100 - $loneliness) * 0.30) +
            ((100 - $friction) * 0.20),
            1
        );

        // Skor Life menggabungkan kemajuan tujuan dengan beban kerja dan
        // tekanan finansial. Dua tekanan terakhir dibalik agar skor tinggi
        // selalu berarti keadaan yang lebih sehat atau terkendali.
        $goalProgress = (int) ($data['goal_progress_score'] ?? 60);
        $workload = (int) ($data['workload_score'] ?? 40);
        $financialPressure = (int) ($data['financial_pressure_score'] ?? 30);

        $lifeScore = round(
            ($goalProgress * 0.45) +
            ((100 - $workload) * 0.30) +
            ((100 - $financialPressure) * 0.25),
            1
        );

        // Semua vector memiliki rentang yang sama, sehingga rata-rata
        // sederhana dipakai sebagai indikator keseluruhan yang mudah dibaca.
        $overallScore = round(($mindScore + $bodyScore + $socialScore + $lifeScore) / 4, 1);

        // Tag dipilih berurutan. Kondisi risiko yang lebih spesifik mendapat
        // prioritas sebelum label positif berdasarkan skor keseluruhan.
        $primaryTag = 'Ritme Seimbang';
        if ($stress > 70 || $overthink > 75) {
            $primaryTag = 'Pikiran Penuh';
        } elseif ($energy < 45 || $sleepHours < 5.5) {
            $primaryTag = 'Tubuh Lelah';
        } elseif ($loneliness > 60) {
            $primaryTag = 'Butuh Teman';
        } elseif ($workload > 80) {
            $primaryTag = 'Beban Padat';
        } elseif ($overallScore >= 75) {
            $primaryTag = 'Kondisi Prima';
        }

        // Simpan ringkasan dan sinyal rinci secara atomik. updateOrCreate
        // membuat request pada tanggal yang sama bersifat idempotent: submit
        // ulang akan memperbarui check-in hari itu, bukan menggandakan baris.
        return DB::transaction(function () use (
            $user, $date, $mindScore, $bodyScore, $socialScore, $lifeScore,
            $overallScore, $data, $primaryTag, $sleepHours, $sleepQuality,
            $activityMin, $energy, $stress, $focus, $overthink, $mood,
            $socialInteraction, $loneliness, $friction, $workload,
            $financialPressure, $goalProgress
        ) {
            $checkin = DailyCheckin::updateOrCreate(
                ['user_id' => $user->id, 'date' => $date],
                [
                    'mind_score' => $mindScore,
                    'body_score' => $bodyScore,
                    'social_score' => $socialScore,
                    'life_score' => $lifeScore,
                    'overall_wellbeing_score' => $overallScore,
                    'notes' => $data['notes'] ?? null,
                    'primary_tag' => $primaryTag,
                ]
            );

            LifeSignal::updateOrCreate(
                ['checkin_id' => $checkin->id],
                [
                    'user_id' => $user->id,
                    'sleep_hours' => $sleepHours,
                    'sleep_quality' => $sleepQuality,
                    'physical_activity_min' => $activityMin,
                    'energy_level' => $energy,
                    'stress_level' => $stress,
                    'focus_level' => $focus,
                    'overthinking_level' => $overthink,
                    'mood_level' => $mood,
                    'social_interaction_score' => $socialInteraction,
                    'loneliness_score' => $loneliness,
                    'relationship_friction_score' => $friction,
                    'workload_score' => $workload,
                    'financial_pressure_score' => $financialPressure,
                    'goal_progress_score' => $goalProgress,
                ]
            );

            return $checkin;
        });
    }

    /**
     * Mengambil histori check-in user untuk data chart dan analisis.
     *
     * Query memuat relationship signal agar skor ringkas dan metrik mentah
     * tersedia dalam satu rangkaian data. Hasil dikembalikan dalam array
     * seri chart, lengkap dengan raw_checkins untuk komponen yang membutuhkan
     * data model aslinya.
     *
     * @param  User  $user  Pemilik data yang boleh dibaca
     * @param  int  $days  Jumlah hari mundur, termasuk hari ini
     * @return array Seri label, vector, sinyal tubuh, dan model check-in mentah
     */
    public function getSignalHistory(User $user, int $days = 14): array
    {
        $checkins = DailyCheckin::with('signal')
            ->where('user_id', $user->id)
            ->where('date', '>=', Carbon::today()->subDays($days - 1)->toDateString())
            ->orderBy('date', 'asc')
            ->get();

        $labels = [];
        $mind = [];
        $body = [];
        $social = [];
        $life = [];
        $overall = [];
        $sleep = [];
        $energy = [];
        $stress = [];

        foreach ($checkins as $c) {
            $labels[] = Carbon::parse($c->date)->translatedFormat('d M');
            $mind[] = (float) $c->mind_score;
            $body[] = (float) $c->body_score;
            $social[] = (float) $c->social_score;
            $life[] = (float) $c->life_score;
            $overall[] = (float) $c->overall_wellbeing_score;
            $sleep[] = $c->signal ? (float) $c->signal->sleep_hours : null;
            $energy[] = $c->signal ? (int) $c->signal->energy_level : null;
            $stress[] = $c->signal ? (int) $c->signal->stress_level : null;
        }

        return [
            'labels' => $labels,
            'mind' => $mind,
            'body' => $body,
            'social' => $social,
            'life' => $life,
            'overall' => $overall,
            'sleep' => $sleep,
            'energy' => $energy,
            'stress' => $stress,
            'raw_checkins' => $checkins,
        ];
    }

    /**
     * Mengambil check-in terbaru milik user beserta detail LifeSignal-nya.
     * Data ini menjadi konteks utama dashboard, chat, dan pemilihan rule
     * refleksi ketika user belum mengirim check-in pada hari berjalan.
     */
    public function getLatestCheckin(User $user): ?DailyCheckin
    {
        return DailyCheckin::with('signal')
            ->where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->first();
    }

    /**
     * Menentukan vector dengan skor terendah dari sebuah check-in.
     * Vector terlemah dipakai sebagai dasar rekomendasi One Small Thing,
     * sehingga micro-action diarahkan ke area yang paling membutuhkan
     * perhatian, bukan dipilih secara acak.
     */
    public function getLowestSignalVector(DailyCheckin $checkin): array
    {
        $vectors = [
            'mind' => [
                'name' => 'Pikiran (Mind)',
                'score' => $checkin->mind_score,
                'color' => '#F59E0B',
                'sub' => 'Stres / Overthinking',
            ],
            'body' => [
                'name' => 'Tubuh (Body)',
                'score' => $checkin->body_score,
                'color' => '#06B6D4',
                'sub' => 'Tidur / Energi Fisik',
            ],
            'social' => [
                'name' => 'Sosial (Social)',
                'score' => $checkin->social_score,
                'color' => '#8B5CF6',
                'sub' => 'Koneksi / Kesepian',
            ],
            'life' => [
                'name' => 'Kehidupan (Life)',
                'score' => $checkin->life_score,
                'color' => '#F43F5E',
                'sub' => 'Beban Kerja / Finansial',
            ],
        ];

        uasort($vectors, fn ($a, $b) => $a['score'] <=> $b['score']);
        $lowestKey = array_key_first($vectors);

        return [
            'key' => $lowestKey,
            'details' => $vectors[$lowestKey],
            'all_sorted' => $vectors,
        ];
    }

    /**
     * Mengambil atau membuat satu micro-action untuk hari ini.
     *
     * Jika check-in tersedia, vector terendah menentukan kelompok aksi.
     * Jika belum ada check-in, body dipakai sebagai default agar dashboard
     * tetap memiliki saran. Aksi disimpan sekali per user per tanggal dan
     * dikembalikan bersama status penyelesaiannya.
     */
    public function getTodayMicroAction(User $user, ?DailyCheckin $latestCheckin): array
    {
        $today = Carbon::today()->toDateString();

        // Default micro action pool per lowest vector
        $vectorKey = 'body';
        if ($latestCheckin) {
            $lowest = $this->getLowestSignalVector($latestCheckin);
            $vectorKey = $lowest['key'];
        }

        $microActions = [
            'body' => [
                'Malam ini: Taruh HP 20 menit sebelum tidur dan redupkan lampu kamar.',
                'Sore ini: Lakukan stretching bahu & leher selama 5 menit.',
                'Siang ini: Minum segelas air putih hangat dan pejamkan mata 3 menit.',
            ],
            'mind' => [
                'Hari ini: Tulis 3 hal yang bikin kepalamu penuh di kertas, lalu pilih 1 yang bisa diabaikan.',
                'Hari ini: Pasang timer 15 menit untuk fokus ke satu tugas kecil tanpa multitasking.',
                'Sore ini: Dengarkan 1 lagu akustik yang paling bikin kamu tenang tanpa membuka medsos.',
            ],
            'social' => [
                'Hari ini: Kirim chat singkat atau meme lucu ke satu teman terdekatmu.',
                'Hari ini: Ucapkan terima kasih tulus ke seseorang yang membantu harimu.',
                'Malam ini: Izinkan dirimu me-time 30 menit tanpa rasa bersalah membalas chat lambat.',
            ],
            'life' => [
                'Hari ini: Pecah tugas terbesarmu menjadi langkah super kecil berdurasi 5 menit.',
                'Hari ini: Beri apresiasi pada dirimu untuk 1 hal kecil yang berhasil kamu selesaikan.',
                'Malam ini: Buat jadwal batas berhenti kerja (hard stop) di jam 22.00.',
            ],
        ];

        $actions = $microActions[$vectorKey] ?? $microActions['body'];
        $selectedAction = $actions[array_rand($actions)];

       
        $log = MicroActionLog::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        if (! $log) {
            try {
                $log = MicroActionLog::create([
                    'user_id' => $user->id,
                    'date' => $today,
                    'action_title' => $selectedAction,
                    'category' => $vectorKey,
                    'is_completed' => false,
                ]);
            } catch (UniqueConstraintViolationException $e) {
                // Seeder or concurrent request already inserted with datetime format — fetch it
                $log = MicroActionLog::where('user_id', $user->id)
                    ->whereDate('date', $today)
                    ->first();
            }
        }

        // Ultimate null guard: return a default action so dashboard never crashes
        if (! $log) {
            return [
                'id' => null,
                'title' => $selectedAction,
                'category' => $vectorKey,
                'is_completed' => false,
                'completed_at' => null,
            ];
        }

        return [
            'id' => $log->id,
            'title' => $log->action_title,
            'category' => $log->category,
            'is_completed' => (bool) $log->is_completed,
            'completed_at' => $log->completed_at,
        ];
    }
}

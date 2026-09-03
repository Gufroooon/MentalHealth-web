<?php

/**
 * Dokumentasi file: Seeder data awal/demo.
 *
 * Menjelaskan tanggung jawab file database/seeders/DemoUserSeeder.php serta hubungan data atau UI-nya dengan bagian aplikasi lain.
 */

namespace Database\Seeders;

use App\Models\DailyCheckin;
use App\Models\InsightPattern;
use App\Models\LifeEvent;
use App\Models\LifeSignal;
use App\Models\Profile;
use App\Models\PulseAggregate;
use App\Models\RecoveryActivity;
use App\Models\RecoverySession;
use App\Models\SupportCircle;
use App\Models\SupportCircleMember;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Demo User
        $user = User::updateOrCreate(
            ['email' => 'nara@wellbeing.id'],
            [
                'name' => 'Alya Pratama',
                'password' => Hash::make('password'),
                'avatar' => 'https://api.dicebear.com/7.x/bottts-neutral/svg?seed=Alya',
            ]
        );

        // 2. Profile
        Profile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'status_role' => 'student',
                'birth_date' => '2003-08-15',
                'participate_pulse' => true,
                'settings_json' => [
                    'theme' => 'light',
                    'notification_email' => true,
                    'weekly_digest' => true,
                ],
            ]
        );

        // 3. Life Events (Timeline)
        LifeEvent::updateOrCreate(
            ['user_id' => $user->id, 'title' => 'Deadline Tugas Besar Sistem Informasi'],
            [
                'category' => 'deadline',
                'start_date' => Carbon::now()->subDays(6)->toDateString(),
                'end_date' => Carbon::now()->subDays(4)->toDateString(),
                'severity_impact' => 5,
                'notes' => 'Tugas kelompok 5 bab dan implementasi prototype.',
            ]
        );

        LifeEvent::updateOrCreate(
            ['user_id' => $user->id, 'title' => 'Presentasi Pitch Deck Magang'],
            [
                'category' => 'work',
                'start_date' => Carbon::now()->subDays(11)->toDateString(),
                'end_date' => Carbon::now()->subDays(10)->toDateString(),
                'severity_impact' => 4,
                'notes' => 'Presentasi di depan mentor dan VP engineering.',
            ]
        );

        // 4. Generate 14-Day Historical Checkins & Signals
        // Days 14 to 8: Normal/Good baseline (sleep ~7.5h, stress ~35, energy ~75)
        // Days 7 to 4: Deadline impact period (sleep ~5.0h, stress ~80, energy ~45, workload ~85)
        // Days 3 to 0 (today): Recovery phase (sleep ~6.8h, stress ~45, energy ~65)

        $historicalData = [
            14 => ['sleep' => 7.8, 'sleep_q' => 80, 'act' => 30, 'energy' => 78, 'stress' => 30, 'focus' => 75, 'overthink' => 25, 'mood' => 80, 'social' => 75, 'lonely' => 20, 'friction' => 10, 'work' => 40, 'fin' => 30, 'goal' => 75, 'notes' => 'Hari tenang, sempat jalan sore.'],
            13 => ['sleep' => 7.5, 'sleep_q' => 75, 'act' => 25, 'energy' => 74, 'stress' => 35, 'focus' => 70, 'overthink' => 30, 'mood' => 75, 'social' => 80, 'lonely' => 15, 'friction' => 10, 'work' => 45, 'fin' => 30, 'goal' => 70, 'notes' => 'Ngopi bareng temen sekelas.'],
            12 => ['sleep' => 7.2, 'sleep_q' => 75, 'act' => 20, 'energy' => 70, 'stress' => 40, 'focus' => 70, 'overthink' => 35, 'mood' => 72, 'social' => 70, 'lonely' => 20, 'friction' => 15, 'work' => 50, 'fin' => 35, 'goal' => 65, 'notes' => 'Mulai persiapan pitch deck.'],
            11 => ['sleep' => 6.2, 'sleep_q' => 65, 'act' => 15, 'energy' => 62, 'stress' => 60, 'focus' => 75, 'overthink' => 50, 'mood' => 65, 'social' => 60, 'lonely' => 25, 'friction' => 15, 'work' => 75, 'fin' => 35, 'goal' => 70, 'notes' => 'Latihan presentasi sampai larut.'],
            10 => ['sleep' => 6.5, 'sleep_q' => 70, 'act' => 20, 'energy' => 68, 'stress' => 55, 'focus' => 80, 'overthink' => 40, 'mood' => 78, 'social' => 75, 'lonely' => 15, 'friction' => 10, 'work' => 65, 'fin' => 30, 'goal' => 85, 'notes' => 'Presentasi sukses dipuji mentor!'],
            9 => ['sleep' => 7.5, 'sleep_q' => 80, 'act' => 35, 'energy' => 76, 'stress' => 30, 'focus' => 70, 'overthink' => 25, 'mood' => 82, 'social' => 85, 'lonely' => 10, 'friction' => 5,  'work' => 40, 'fin' => 30, 'goal' => 80, 'notes' => 'Weekend rehat santai.'],
            8 => ['sleep' => 8.0, 'sleep_q' => 85, 'act' => 40, 'energy' => 80, 'stress' => 25, 'focus' => 75, 'overthink' => 20, 'mood' => 85, 'social' => 80, 'lonely' => 10, 'friction' => 5,  'work' => 35, 'fin' => 30, 'goal' => 75, 'notes' => 'Tidur nyenyak banget.'],

            // Period 2: Deadline week
            7 => ['sleep' => 6.0, 'sleep_q' => 60, 'act' => 10, 'energy' => 58, 'stress' => 65, 'focus' => 65, 'overthink' => 55, 'mood' => 60, 'social' => 50, 'lonely' => 30, 'friction' => 20, 'work' => 78, 'fin' => 40, 'goal' => 60, 'notes' => 'Pengumuman deadline dimajukan.'],
            6 => ['sleep' => 5.2, 'sleep_q' => 50, 'act' => 5,  'energy' => 48, 'stress' => 78, 'focus' => 60, 'overthink' => 70, 'mood' => 52, 'social' => 40, 'lonely' => 45, 'friction' => 25, 'work' => 88, 'fin' => 45, 'goal' => 55, 'notes' => 'Ngebut coding tugas kelompok.'],
            5 => ['sleep' => 4.8, 'sleep_q' => 40, 'act' => 5,  'energy' => 42, 'stress' => 85, 'focus' => 55, 'overthink' => 75, 'mood' => 45, 'social' => 35, 'lonely' => 50, 'friction' => 30, 'work' => 92, 'fin' => 45, 'goal' => 50, 'notes' => 'Begadang bareng tim, pusing.'],
            4 => ['sleep' => 5.5, 'sleep_q' => 50, 'act' => 10, 'energy' => 50, 'stress' => 75, 'focus' => 65, 'overthink' => 65, 'mood' => 55, 'social' => 50, 'lonely' => 35, 'friction' => 20, 'work' => 85, 'fin' => 40, 'goal' => 65, 'notes' => 'Tugas terkirim tepat waktu, lega tapi tepar.'],
            3 => ['sleep' => 7.0, 'sleep_q' => 70, 'act' => 20, 'energy' => 64, 'stress' => 48, 'focus' => 68, 'overthink' => 40, 'mood' => 68, 'social' => 65, 'lonely' => 25, 'friction' => 10, 'work' => 50, 'fin' => 35, 'goal' => 65, 'notes' => 'Mulai tidur lebih teratur.'],
            2 => ['sleep' => 7.2, 'sleep_q' => 75, 'act' => 25, 'energy' => 70, 'stress' => 40, 'focus' => 72, 'overthink' => 35, 'mood' => 74, 'social' => 70, 'lonely' => 20, 'friction' => 10, 'work' => 45, 'fin' => 30, 'goal' => 70, 'notes' => 'Jalan sore 25 menit keliling komplek.'],
            1 => ['sleep' => 7.4, 'sleep_q' => 78, 'act' => 20, 'energy' => 72, 'stress' => 38, 'focus' => 75, 'overthink' => 30, 'mood' => 76, 'social' => 75, 'lonely' => 18, 'friction' => 10, 'work' => 42, 'fin' => 30, 'goal' => 72, 'notes' => 'Kemarin ngerasa jauh lebih segar.'],
            0 => ['sleep' => 7.5, 'sleep_q' => 80, 'act' => 25, 'energy' => 75, 'stress' => 35, 'focus' => 78, 'overthink' => 28, 'mood' => 80, 'social' => 78, 'lonely' => 15, 'friction' => 5,  'work' => 40, 'fin' => 30, 'goal' => 75, 'notes' => 'Hari ini ritme hidup mulai stabil kembali.'],
        ];

        foreach ($historicalData as $daysAgo => $data) {
            $date = Carbon::now()->subDays($daysAgo)->toDateString();

            // Calculate 4 vector scores
            // Mind: mood (40%) + focus (30%) + (100 - stress)*15% + (100 - overthink)*15%
            $mindScore = round(($data['mood'] * 0.4) + ($data['focus'] * 0.3) + ((100 - $data['stress']) * 0.15) + ((100 - $data['overthink']) * 0.15), 1);

            // Body: sleep_q (35%) + energy (35%) + min(100, act*2) (15%) + min(100, (sleep/8)*100) (15%)
            $sleepDurScore = min(100, ($data['sleep'] / 8.0) * 100);
            $actScore = min(100, $data['act'] * 2.5);
            $bodyScore = round(($data['sleep_q'] * 0.35) + ($data['energy'] * 0.35) + ($actScore * 0.15) + ($sleepDurScore * 0.15), 1);

            // Social: social (50%) + (100 - lonely)*30% + (100 - friction)*20%
            $socialScore = round(($data['social'] * 0.5) + ((100 - $data['lonely']) * 0.3) + ((100 - $data['friction']) * 0.2), 1);

            // Life: goal (45%) + (100 - work)*30% + (100 - fin)*25%
            $lifeScore = round(($data['goal'] * 0.45) + ((100 - $data['work']) * 0.3) + ((100 - $data['fin']) * 0.25), 1);

            $overall = round(($mindScore + $bodyScore + $socialScore + $lifeScore) / 4, 1);

            $checkin = DailyCheckin::updateOrCreate(
                ['user_id' => $user->id, 'date' => $date],
                [
                    'mind_score' => $mindScore,
                    'body_score' => $bodyScore,
                    'social_score' => $socialScore,
                    'life_score' => $lifeScore,
                    'overall_wellbeing_score' => $overall,
                    'notes' => $data['notes'],
                    'primary_tag' => $data['stress'] > 70 ? 'Stres Deadline' : ($data['energy'] > 75 ? 'Hari Prima' : 'Ritme Seimbang'),
                ]
            );

            LifeSignal::updateOrCreate(
                ['checkin_id' => $checkin->id],
                [
                    'user_id' => $user->id,
                    'sleep_hours' => $data['sleep'],
                    'sleep_quality' => $data['sleep_q'],
                    'physical_activity_min' => $data['act'],
                    'energy_level' => $data['energy'],
                    'stress_level' => $data['stress'],
                    'focus_level' => $data['focus'],
                    'overthinking_level' => $data['overthink'],
                    'mood_level' => $data['mood'],
                    'social_interaction_score' => $data['social'],
                    'loneliness_score' => $data['lonely'],
                    'relationship_friction_score' => $data['friction'],
                    'workload_score' => $data['work'],
                    'financial_pressure_score' => $data['fin'],
                    'goal_progress_score' => $data['goal'],
                ]
            );
        }

        // 5. Recovery Sessions History (for dynamic recovery profile ranking)
        $actWalk = RecoveryActivity::where('name', 'like', '%Jalan Santai%')->first();
        $actMusic = RecoveryActivity::where('name', 'like', '%Dengerin Playlist%')->first();
        $actNap = RecoveryActivity::where('name', 'like', '%Power Nap%')->first();
        $actJournal = RecoveryActivity::where('name', 'like', '%Curhat Tulis%')->first();

        if ($actWalk) {
            RecoverySession::create([
                'user_id' => $user->id,
                'activity_id' => $actWalk->id,
                'energy_before' => 45,
                'energy_after' => 68,
                'mood_before' => 50,
                'mood_after' => 78,
                'duration_minutes' => 20,
                'notes' => 'Jalan sore di taman komplek bikin pikiran adem.',
                'created_at' => Carbon::now()->subDays(2),
            ]);
            RecoverySession::create([
                'user_id' => $user->id,
                'activity_id' => $actWalk->id,
                'energy_before' => 50,
                'energy_after' => 72,
                'mood_before' => 55,
                'mood_after' => 80,
                'duration_minutes' => 15,
                'notes' => 'Keluar kamar tanpa bawa HP, segar banget.',
                'created_at' => Carbon::now()->subDays(8),
            ]);
        }

        if ($actMusic) {
            RecoverySession::create([
                'user_id' => $user->id,
                'activity_id' => $actMusic->id,
                'energy_before' => 52,
                'energy_after' => 66,
                'mood_before' => 48,
                'mood_after' => 74,
                'duration_minutes' => 15,
                'notes' => 'Dengerin lagu instrumental lofi.',
                'created_at' => Carbon::now()->subDays(4),
            ]);
        }

        if ($actNap) {
            RecoverySession::create([
                'user_id' => $user->id,
                'activity_id' => $actNap->id,
                'energy_before' => 38,
                'energy_after' => 62,
                'mood_before' => 45,
                'mood_after' => 65,
                'duration_minutes' => 20,
                'notes' => 'Power nap jam 2 siang, bangun langsung seger.',
                'created_at' => Carbon::now()->subDays(5),
            ]);
        }

        if ($actJournal) {
            RecoverySession::create([
                'user_id' => $user->id,
                'activity_id' => $actJournal->id,
                'energy_before' => 55,
                'energy_after' => 65,
                'mood_before' => 40,
                'mood_after' => 70,
                'duration_minutes' => 10,
                'notes' => 'Tulis semua ketakutan di kertas lalu disobek.',
                'created_at' => Carbon::now()->subDays(6),
            ]);
        }

        // 6. Support Circle & Members
        $circle = SupportCircle::firstOrCreate(
            ['user_id' => $user->id],
            ['circle_name' => 'Lingkaran Aman Alya']
        );

        SupportCircleMember::updateOrCreate(
            ['circle_id' => $circle->id, 'name' => 'Dimas Arya'],
            [
                'email' => 'dimas@example.com',
                'phone' => '081234567890',
                'relationship_type' => 'sahabat',
                'is_active' => true,
            ]
        );

        SupportCircleMember::updateOrCreate(
            ['circle_id' => $circle->id, 'name' => 'Kak Rian'],
            [
                'email' => 'rian@example.com',
                'phone' => '081298765432',
                'relationship_type' => 'keluarga',
                'is_active' => true,
            ]
        );

        SupportCircleMember::updateOrCreate(
            ['circle_id' => $circle->id, 'name' => 'Nadia Salsa'],
            [
                'email' => 'nadia@example.com',
                'phone' => '085712345678',
                'relationship_type' => 'sahabat',
                'is_active' => true,
            ]
        );

        // 7. Insight Patterns
        InsightPattern::updateOrCreate(
            ['user_id' => $user->id, 'pattern_type' => 'event_correlation'],
            [
                'title' => 'Pola Menjelang Deadline: Jam Tidur Menurun 28% & Beban Stres Meningkat',
                'description_json' => [
                    'summary' => '3–5 hari sebelum deadline tugas besar, durasi tidurmu rata-rata turun dari 7.5 jam menjadi 5.1 jam. Hal ini memicu penurunan energi sebesar 34% dan lonjakan overthinking.',
                    'chain' => [
                        'Beban Tugas Meningkat (85%)',
                        'Jam Tidur Turun (5.1 jam)',
                        'Energi Drop (42%)',
                        'Overthinking & Stres Naik (85%)',
                    ],
                    'recommendation' => 'Coba batasi jam lembur maksimal jam 23:00 dan pecah tugas besar menjadi target mikro harian.',
                ],
                'detected_at' => Carbon::now()->subDays(4),
                'status' => 'active',
            ]
        );

        InsightPattern::updateOrCreate(
            ['user_id' => $user->id, 'pattern_type' => 'recovery_booster'],
            [
                'title' => 'Pemulihan Paling Efektif: Jalan Santai Luar Ruangan (+22 Poin Energi)',
                'description_json' => [
                    'summary' => 'Data mencatat bahwa aktivitas jalan santai 15-20 menit di luar ruangan secara konsisten mendongkrak energimu rata-rata +22 poin dan mood +26 poin.',
                    'chain' => [
                        'Jalan Santai Sore (15-20 min)',
                        'Mood Naik Signifikan (+26)',
                        'Energi Pulih (+22)',
                        'Stres Berkurang Cepat',
                    ],
                    'recommendation' => 'Jadikan jalan sore sebagai ritual pemulihan default setiap kali energimu berada di bawah 50.',
                ],
                'detected_at' => Carbon::now()->subDays(2),
                'status' => 'active',
            ]
        );

        // 8. Pulse Aggregates (Youth Community Data)
        $currentWeek = Carbon::now()->weekOfYear;
        $currentYear = Carbon::now()->year;

        $pulseStats = [
            ['metric' => 'academic_pressure', 'value' => 74.5, 'sample' => 342, 'meta' => ['top_challenge' => 'Tenggat Waktu Tugas & Ujian', 'label' => 'Tekanan Beban Akademik / Kerja']],
            ['metric' => 'sleep_deficit', 'value' => 68.2, 'sample' => 342, 'meta' => ['top_challenge' => 'Rata-rata tidur < 6 jam di hari kerja', 'label' => 'Defisit Jam Tidur']],
            ['metric' => 'overthinking', 'value' => 61.8, 'sample' => 342, 'meta' => ['top_challenge' => 'Kecemasan tentang arah karir & masa depan', 'label' => 'Tingkat Overthinking']],
            ['metric' => 'social_loneliness', 'value' => 43.0, 'sample' => 342, 'meta' => ['top_challenge' => 'Merasa jauh dari lingkungan sosial', 'label' => 'Rasa Terisolasi / Kesepian']],
            ['metric' => 'recovery_satisfaction', 'value' => 78.4, 'sample' => 342, 'meta' => ['top_challenge' => 'Aktivitas pemulihan terbukti membantu relaksasi', 'label' => 'Efektivitas Waktu Rehat']],
        ];

        foreach ($pulseStats as $p) {
            PulseAggregate::updateOrCreate(
                [
                    'week_number' => $currentWeek,
                    'year' => $currentYear,
                    'role_filter' => 'all',
                    'metric_name' => $p['metric'],
                ],
                [
                    'aggregate_value' => $p['value'],
                    'sample_count' => $p['sample'],
                    'meta_json' => $p['meta'],
                ]
            );

            // Also for student role
            PulseAggregate::updateOrCreate(
                [
                    'week_number' => $currentWeek,
                    'year' => $currentYear,
                    'role_filter' => 'student',
                    'metric_name' => $p['metric'],
                ],
                [
                    'aggregate_value' => $p['value'] + rand(-2, 3),
                    'sample_count' => 195,
                    'meta_json' => $p['meta'],
                ]
            );
        }
    }
}

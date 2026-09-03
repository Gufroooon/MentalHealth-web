<?php

/**
 * Dokumentasi file: Seeder data awal/demo.
 *
 * Menjelaskan tanggung jawab file database/seeders/KnowledgeBaseSeeder.php serta hubungan data atau UI-nya dengan bagian aplikasi lain.
 */

namespace Database\Seeders;

use App\Models\KnowledgeBaseRule;
use Illuminate\Database\Seeder;

class KnowledgeBaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rules = [
            // --- 1. BODY & SLEEP DEFICIT ---
            [
                'category' => 'body',
                'title' => 'Tubuh Lelah & Jam Tidur Menipis',
                'trigger_conditions_json' => ['sleep_hours_max' => 5.5, 'energy_max' => 50],
                'reflection_prompt' => 'Kelihatannya beberapa hari ini energimu lagi terkuras dan jam tidurmu kurang dari biasanya. Nggak apa-apa kalau hari ini ritmemu terasa lambat, tubuhmu memang lagi ngirim sinyal butuh rehat.',
                'guided_question' => 'Apa satu hal yang malam ini bisa kamu tunda atau kurangi, supaya kamu bisa rebahan 30 menit lebih awal?',
                'action_title' => 'Jeda Layar 20 Menit Sebelum Tidur',
                'action_suggestion' => 'Malam ini, coba taruh HP di meja yang agak jauh dari kasur 20 menit sebelum tidur. Biarkan matamu rileks.',
                'action_suggestion_id' => 'act_body_screen_sleep',
                'priority' => 1,
            ],
            [
                'category' => 'body',
                'title' => 'Kurang Gerak Fisik & Tubuh Kaku',
                'trigger_conditions_json' => ['physical_activity_max' => 10, 'energy_max' => 60],
                'reflection_prompt' => 'Duduk berjam-jam di depan laptop atau kasur kadang bikin energi kita stagnan dan tubuh terasa berat.',
                'guided_question' => 'Bagian tubuh mana yang paling terasa pegal hari ini? Pundak, leher, atau pinggang?',
                'action_title' => 'Stretching Ringan 5 Menit',
                'action_suggestion' => 'Berdiri sekarang, putar bahu ke belakang 10 kali, lalu rentangkan tangan ke atas sambil tarik napas dalam.',
                'action_suggestion_id' => 'act_body_stretch',
                'priority' => 2,
            ],
            [
                'category' => 'body',
                'title' => 'Kualitas Tidur Kurang Nyenyak',
                'trigger_conditions_json' => ['sleep_quality_max' => 45],
                'reflection_prompt' => 'Tidur tapi bangun masih capek rasanya menyebalkan ya. Kualitas tidur sering terganggu oleh suhu ruangan, lampu, atau pikiran yang belum selesai.',
                'guided_question' => 'Sebelum tidur semalam, apa hal terakhir yang bikin kepalamu masih mikir keras?',
                'action_title' => 'Minum Air Hangat & Redupkan Lampu Kamar',
                'action_suggestion' => 'Bikin suasana kamar segelap dan seadem mungkin nanti malam, plus segelas air putih hangat di samping tempat tidur.',
                'action_suggestion_id' => 'act_body_sleep_env',
                'priority' => 3,
            ],

            // --- 2. MIND & OVERTHINKING / STRESS ---
            [
                'category' => 'mind',
                'title' => 'Pikiran Penuh & Overthinking',
                'trigger_conditions_json' => ['overthinking_min' => 70, 'stress_min' => 60],
                'reflection_prompt' => 'Isi kepala lagi ramai banget ya? Wajar kok kalau rasanya semua skenario buruk ingin dipikirkan sekaligus. Tapi ingat: pikiran kita bukan fakta masa depan.',
                'guided_question' => 'Dari semua hal yang kamu cemaskan hari ini, mana satu hal yang BENAR-BENAR ada di dalam kendalimu saat ini juga?',
                'action_title' => 'Teknik Braindump Kertas 5 Menit',
                'action_suggestion' => 'Ambil secarik kertas, tuliskan 3 hal yang paling bikin cemas. Lalu lingkari satu saja yang bisa kamu kerjakan besok.',
                'action_suggestion_id' => 'act_mind_braindump',
                'priority' => 1,
            ],
            [
                'category' => 'mind',
                'title' => 'Tingkat Fokus Lagi Drop',
                'trigger_conditions_json' => ['focus_max' => 40, 'stress_min' => 50],
                'reflection_prompt' => 'Sulit konsentrasi itu tanda otak kita lagi kelebihan muatan info (*cognitive overload*), bukan karena kamu malas.',
                'guided_question' => 'Apakah kamu lagi mencoba multitasking terlalu banyak tugas hari ini?',
                'action_title' => 'Metode Single-Tasking 20 Menit',
                'action_suggestion' => 'Pilih SATU tugas terkecil. Pasang timer 20 menit, tutup semua tab lain yang nggak berkaitan.',
                'action_suggestion_id' => 'act_mind_pomodoro',
                'priority' => 2,
            ],
            [
                'category' => 'mind',
                'title' => 'Mood Sedang Abu-Abu / Flat',
                'trigger_conditions_json' => ['mood_max' => 45],
                'reflection_prompt' => 'Ada hari-hari di mana perasaan kita biasa aja atau bahkan agak hampa, dan itu sah-sah saja. Nggak perlu memaksa diri langsung ceria.',
                'guided_question' => 'Kira-kira apa hal sederhana yang terakhir kali bisa bikin kamu tersenyum kecil minggu ini?',
                'action_title' => 'Dengerin 1 Lagu Favorit Penenang',
                'action_suggestion' => 'Pasang earphone, putar satu lagu yang paling bikin kamu merasa nyaman, dan nikmati nadanya.',
                'action_suggestion_id' => 'act_mind_music',
                'priority' => 3,
            ],

            // --- 3. SOCIAL & LONELINESS ---
            [
                'category' => 'social',
                'title' => 'Rasa Sepi & Butuh Terhubung',
                'trigger_conditions_json' => ['loneliness_min' => 65],
                'reflection_prompt' => 'Rasa kesepian kadang muncul bahkan di tengah keramaian. Membutuhkan interaksi hangat adalah kebutuhan dasar manusia yang sangat normal.',
                'guided_question' => 'Siapa satu orang terdekat yang biasanya bikin kamu merasa didengar tanpa dihakimi?',
                'action_title' => 'Kirim Satu Pesan Santai / Meme',
                'action_suggestion' => 'Kirim chat sederhana ke teman dekatmu: "Lagi senggang nggak? Cuma mau nyapa aja." atau share meme lucu.',
                'action_suggestion_id' => 'act_social_ping_friend',
                'priority' => 1,
            ],
            [
                'category' => 'social',
                'title' => 'Gesekan / Konflik Hubungan',
                'trigger_conditions_json' => ['friction_min' => 60],
                'reflection_prompt' => 'Ketegangan atau salah paham dengan teman, keluarga, atau pasangan memang menyerap energi batin yang luar biasa besar.',
                'guided_question' => 'Apakah saat ini lebih baik mengambil jeda sejenak untuk menenangkan diri sebelum melanjutkan obrolan?',
                'action_title' => 'Jeda Waktu Respons (Cooling Down)',
                'action_suggestion' => 'Tarik napas 3 kali sebelum membalas pesan yang memicu emosi. Nggak apa-apa minta waktu jeda 1 jam.',
                'action_suggestion_id' => 'act_social_cooldown',
                'priority' => 2,
            ],
            [
                'category' => 'social',
                'title' => 'Social Battery Lagi Low / Overstimulated',
                'trigger_conditions_json' => ['social_interaction_max' => 40, 'energy_max' => 45],
                'reflection_prompt' => 'Baterai sosialmu kelihatannya lagi butuh di-charge. Mengisolasi diri sejenak untuk istirahat itu bukan egois kok, itu *self-care*.',
                'guided_question' => 'Apa aktivitas '.'me-time'.' yang paling bisa mengembalikan energimu saat sendirian?',
                'action_title' => 'Quiet Space 15 Menit',
                'action_suggestion' => 'Matikan notifikasi grup chat selama 1 jam ke depan dan nikmati ketenangan tanpa tuntutan membalas cepat.',
                'action_suggestion_id' => 'act_social_quiet',
                'priority' => 3,
            ],

            // --- 4. LIFE & WORKLOAD / FINANCIAL PRESSURE ---
            [
                'category' => 'life',
                'title' => 'Beban Tugas & Deadline Menumpuk',
                'trigger_conditions_json' => ['workload_min' => 75],
                'reflection_prompt' => 'Tumpukan tugas atau deadline memang bisa bikin kita merasa sesak napas. Tapi ingat, kamu nggak harus menyelesaikan semuanya sekaligus dalam satu jam.',
                'guided_question' => 'Kalau harus memilih satu saja yang paling mendesak hari ini, mana tugas yang bakal kamu selesaikan duluan?',
                'action_title' => 'Pecah Tugas Jadi Langkah 5 Menit',
                'action_suggestion' => 'Pecah tugas terberatmu jadi langkah pertama yang super kecil (misal: cuma buka Google Docs dan tulis judulnya).',
                'action_suggestion_id' => 'act_life_small_step',
                'priority' => 1,
            ],
            [
                'category' => 'life',
                'title' => 'Tekanan Finansial / Pengeluaran Tak Terduga',
                'trigger_conditions_json' => ['financial_pressure_min' => 70],
                'reflection_prompt' => 'Kekhawatiran soal uang atau masa depan adalah salah satu beban paling umum yang dialami anak muda saat ini. Jangan merasa gagal sendirian.',
                'guided_question' => 'Hal apa yang saat ini bisa kamu kendalikan dalam 24 jam ke depan terkait pengeluaran harian?',
                'action_title' => 'Audit Pengeluaran Harian Santai',
                'action_suggestion' => 'Buka catatan kecil, tulis 1 kebutuhan esensial hari ini, dan hindari membuka aplikasi e-commerce / diskon impulsif malam ini.',
                'action_suggestion_id' => 'act_life_budget_pause',
                'priority' => 2,
            ],
            [
                'category' => 'life',
                'title' => 'Progres Tujuan Terasa Macet',
                'trigger_conditions_json' => ['goal_progress_max' => 35],
                'reflection_prompt' => 'Merasa jalan di tempat itu proses alami dalam bertumbuh. Perjalanan nggak selalu naik lurus, ada fase konsolidasi.',
                'guided_question' => 'Apakah standarmu untuk dirimu sendiri hari ini mungkin lagi terlalu tinggi?',
                'action_title' => 'Apresiasi 1 Kemenangan Kecil',
                'action_suggestion' => 'Sebutkan 1 hal kecil yang berhasil kamu lakukan hari ini—bahkan sekadar bangun pagi atau minum air yang cukup.',
                'action_suggestion_id' => 'act_life_small_win',
                'priority' => 3,
            ],

            // --- 5. COMBINATIONS (MULTI-VECTOR PATTERNS) ---
            [
                'category' => 'combination',
                'title' => 'Kombinasi: Kurang Tidur + Beban Kerja Tinggi',
                'trigger_conditions_json' => ['sleep_hours_max' => 6.0, 'workload_min' => 70],
                'reflection_prompt' => 'Pola klasik yang sering terjadi: beban kerja tinggi memaksa kita mengorbankan jam tidur. Akibatnya, esok harinya kita butuh usaha 2x lipat untuk fokus.',
                'guided_question' => 'Bagaimana jika malam ini kamu pasang '.'hard stop'.' jam kerja pada jam 22.00?',
                'action_title' => 'Tutup Laptop Tepat Waktu Malam Ini',
                'action_suggestion' => 'Tentukan jam berhenti kerja malam ini, lalu simpan file tugasmu dan tinggalkan meja kerja.',
                'action_suggestion_id' => 'act_combo_hard_stop',
                'priority' => 1,
            ],
            [
                'category' => 'combination',
                'title' => 'Kombinasi: Overthinking + Kesepian',
                'trigger_conditions_json' => ['overthinking_min' => 65, 'loneliness_min' => 60],
                'reflection_prompt' => 'Saat kita merasa sendiri, suara overthinking di kepala biasanya terdengar 2 kali lebih keras. Padahal banyak orang di luar sana yang peduli padamu.',
                'guided_question' => 'Jika teman terbaikmu mengalami hal persis seperti yang kamu rasakan sekarang, nasihat hangat apa yang akan kamu katakan padanya?',
                'action_title' => 'Bicara Lembut pada Diri Sendiri',
                'action_suggestion' => 'Katakan pada dirimu: "Aku sedang berusaha sebaik mungkin, dan itu sudah cukup untuk hari ini."',
                'action_suggestion_id' => 'act_combo_self_compassion',
                'priority' => 1,
            ],
            [
                'category' => 'combination',
                'title' => 'Kombinasi: Stres Akademik / Karir + Kurang Gerak',
                'trigger_conditions_json' => ['stress_min' => 65, 'physical_activity_max' => 15],
                'reflection_prompt' => 'Hormon kortisol dari stres menumpuk di tubuh jika tidak disalurkan lewat gerakan fisik sederhana.',
                'guided_question' => 'Maukah kamu memberi waktu 10 menit untuk tubuhmu bergerak ringan sebelum lanjut berpikir?',
                'action_title' => 'Jalan Santai Keluar Ruangan',
                'action_suggestion' => 'Keluar dari kamar atau ruangan, lihat tanaman hijau atau langit sore selama 10 menit.',
                'action_suggestion_id' => 'act_combo_walk_nature',
                'priority' => 2,
            ],

            // --- 6. BALANCED / POSITIVE STATE (AFFIRMATION & STABILITY) ---
            [
                'category' => 'mind',
                'title' => 'Kondisi Prima & Energi Stabil',
                'trigger_conditions_json' => ['overall_min' => 75],
                'reflection_prompt' => 'Keren banget! Sinyal hidupmu hari ini terlihat sangat seimbang dan stabil. Energi dan fokusmu lagi di titik yang baik.',
                'guided_question' => 'Kebiasaan baik apa yang kamu lakukan kemarin yang paling berkontribusi pada energi positifmu hari ini?',
                'action_title' => 'Catat Resep Sukses Harimu',
                'action_suggestion' => 'Simpan pola baik ini di ingatanmu supaya bisa diulangi lagi saat harimu terasa berat nanti.',
                'action_suggestion_id' => 'act_mind_anchor_positive',
                'priority' => 4,
            ],
            [
                'category' => 'body',
                'title' => 'Tidur Nyenyak & Tubuh Segar',
                'trigger_conditions_json' => ['sleep_hours_min' => 7.5, 'sleep_quality_min' => 75],
                'reflection_prompt' => 'Tidur yang cukup membawa pengaruh luar biasa ke kejernihan pikiran dan kestabilan emosi hari ini.',
                'guided_question' => 'Manfaatkan energi segar ini untuk apa hal terpenting yang ingin kamu capai hari ini?',
                'action_title' => 'Tuntaskan Tugas Prioritas Utama',
                'action_suggestion' => 'Gunakan momen energi puncak pagi ini untuk menyelesaikan hal yang paling menantang.',
                'action_suggestion_id' => 'act_body_morning_peak',
                'priority' => 4,
            ],
            [
                'category' => 'social',
                'title' => 'Koneksi Sosial Hangat & Bermakna',
                'trigger_conditions_json' => ['social_interaction_min' => 75, 'loneliness_max' => 25],
                'reflection_prompt' => 'Interaksi sosial yang positif hari ini memberi bahan bakar emosional yang sangat sehat.',
                'guided_question' => 'Siapa yang paling membuatmu merasa bersyukur telah hadir dalam lingkaran hidupmu minggu ini?',
                'action_title' => 'Kirim Ucapan Terima Kasih Sederhana',
                'action_suggestion' => 'Sampaikan terima kasih tulus ke teman atau keluargamu atas bantuan atau kehadiran mereka.',
                'action_suggestion_id' => 'act_social_gratitude',
                'priority' => 4,
            ],
        ];

        foreach ($rules as $rule) {
            KnowledgeBaseRule::updateOrCreate(
                ['title' => $rule['title']],
                $rule
            );
        }
    }
}

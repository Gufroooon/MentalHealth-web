<?php

/**
 * Dokumentasi file: Seeder data awal/demo.
 *
 * Menjelaskan tanggung jawab file database/seeders/RecoveryActivitySeeder.php serta hubungan data atau UI-nya dengan bagian aplikasi lain.
 */

namespace Database\Seeders;

use App\Models\RecoveryActivity;
use Illuminate\Database\Seeder;

class RecoveryActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activities = [
            [
                'name' => 'Jalan Santai Sore Tanpa Earphone',
                'icon' => 'footprints',
                'category' => 'physical',
                'description' => 'Jalan santai di sekitar rumah/taman 10-15 menit untuk melepaskan penat dan menyerap udara segar.',
                'default_duration_min' => 15,
            ],
            [
                'name' => 'Power Nap Singkat (15-20 Menit)',
                'icon' => 'moon',
                'category' => 'physical',
                'description' => 'Tidur siang sebentar sebelum jam 3 sore untuk me-recharge fokus tanpa bikin pusing saat bangun.',
                'default_duration_min' => 20,
            ],
            [
                'name' => 'Dengerin Playlist Lagu Akustik / Alam',
                'icon' => 'musical-note',
                'category' => 'sensory',
                'description' => 'Duduk santai di tempat nyaman sambil dengerin suara hujan, instrumental, atau lagu favorit.',
                'default_duration_min' => 15,
            ],
            [
                'name' => 'Curhat Tulis / Braindump Bebas',
                'icon' => 'pencil-square',
                'category' => 'mental',
                'description' => 'Tuliskan semua pikiran yang bikin ruwet di secarik kertas tanpa diedit, lalu tarik napas panjang.',
                'default_duration_min' => 10,
            ],
            [
                'name' => 'Minum Teh Hangat & Stretching Ringan',
                'icon' => 'cup-hot',
                'category' => 'sensory',
                'description' => 'Seduh teh herbal/chamomile hangat dan lakukan peregangan leher serta pundak yang kaku.',
                'default_duration_min' => 10,
            ],
            [
                'name' => 'Ngobrol Santai Sama Sahabat / Keluarga',
                'icon' => 'chat-bubble',
                'category' => 'social',
                'description' => 'Telepon atau chat singkat tanpa membahas beban kerja, cukup bercanda atau tanya kabar.',
                'default_duration_min' => 20,
            ],
            [
                'name' => 'Latihan Pernapasan 4-7-8 Tenang',
                'icon' => 'heart',
                'category' => 'mental',
                'description' => 'Tarik napas 4 detik, tahan 7 detik, buang perlahan 8 detik selama 4 siklus untuk meredakan denyut stres.',
                'default_duration_min' => 5,
            ],
            [
                'name' => 'Mandi Air Hangat & Cuci Muka',
                'icon' => 'sparkles',
                'category' => 'physical',
                'description' => 'Rilekskan otot-otot tubuh dengan guyuran air hangat yang menenangkan.',
                'default_duration_min' => 15,
            ],
            [
                'name' => 'Digital Detox 30 Menit',
                'icon' => 'device-phone-mobile',
                'category' => 'mental',
                'description' => 'Simpan HP di ruangan lain, baca buku fisik, atau sekadar memandangi langit.',
                'default_duration_min' => 30,
            ],
            [
                'name' => 'Merapikan Meja Belajar / Kerja',
                'icon' => 'archive-box',
                'category' => 'creative',
                'description' => 'Ruang kerja yang bersih secara instan mengurangi beban kognitif dan overthinking.',
                'default_duration_min' => 15,
            ],
        ];

        foreach ($activities as $act) {
            RecoveryActivity::firstOrCreate(
                ['name' => $act['name']],
                $act
            );
        }
    }
}

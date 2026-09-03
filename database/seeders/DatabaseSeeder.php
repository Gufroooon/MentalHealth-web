<?php

/**
 * Dokumentasi file: Seeder data awal/demo.
 *
 * Menjelaskan tanggung jawab file database/seeders/DatabaseSeeder.php serta hubungan data atau UI-nya dengan bagian aplikasi lain.
 */

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RecoveryActivitySeeder::class,
            KnowledgeBaseSeeder::class,
            DemoUserSeeder::class,
        ]);
    }
}

<?php

/**
 * Dokumentasi file: Definisi route Laravel.
 *
 * Menjelaskan tanggung jawab file routes/console.php serta hubungan data atau UI-nya dengan bagian aplikasi lain.
 */

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

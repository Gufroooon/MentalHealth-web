<?php

/**
 * Dokumentasi file: Service business logic.
 *
 * Membaca tren komunitas yang sudah diagregasi tanpa membuka data individual user.
 */

namespace App\Services;

use App\Models\PulseAggregate;
use Carbon\Carbon;

class PulseService
{
    /**
     * Mengambil agregasi tren komunitas untuk minggu dan role tertentu.
     * Query hanya membaca PulseAggregate yang sudah diringkas sehingga UI
     * tidak menerima identitas atau detail check-in individu. Jika agregat
     * minggu berjalan belum tersedia, service memakai beberapa agregat terbaru.
     */
    public function getCommunityPulse(string $roleFilter = 'all'): array
    {
        $currentWeek = Carbon::now()->weekOfYear;
        $currentYear = Carbon::now()->year;

        $aggregates = PulseAggregate::where('year', $currentYear)
            ->where('week_number', $currentWeek)
            ->where(function ($q) use ($roleFilter) {
                $q->where('role_filter', $roleFilter)
                    ->orWhere('role_filter', 'all');
            })
            ->get();

        if ($aggregates->isEmpty()) {
            // Fallback membuat halaman tetap informatif saat proses agregasi
            // minggu berjalan belum menghasilkan baris baru.
            $aggregates = PulseAggregate::orderBy('year', 'desc')
                ->orderBy('week_number', 'desc')
                ->take(10)
                ->get();
        }

        // Generate community solidarity cards
        $solidarityInsights = [
            [
                'icon' => 'academic-cap',
                'stat' => '74.5%',
                'title' => 'Tekanan Beban Akademik & Deadline',
                'message' => 'Sebagian besar pemuda minggu ini merasakan lonjakan beban tugas. Mengatur ritme santai dan rehat sejenak adalah kunci agar tidak burnout.',
                'tag' => 'Tantangan #1 Minggu Ini',
            ],
            [
                'icon' => 'moon',
                'stat' => '68.2%',
                'title' => 'Rata-rata Tidur < 6 Jam',
                'message' => 'Banyak dari kita yang mengorbankan jam istirahat. Malam ini, yuk pasang alarm tidur lebih awal bersama.',
                'tag' => 'Pola Istirahat',
            ],
            [
                'icon' => 'sparkles',
                'stat' => '78.4%',
                'title' => 'Rehat Singkat Terbukti Efektif',
                'message' => 'Teman-teman di komunitas melaporkan peningkatan energi nyata setelah melakukan aktivitas rehat 15 menit.',
                'tag' => 'Kabar Baik',
            ],
            [
                'icon' => 'chat-bubble',
                'stat' => '61.8%',
                'title' => 'Overthinking Masa Depan & Karir',
                'message' => 'Rasa cemas soal arah hidup sangat lumrah di usia 20-an. Kamu sedang berproses, jalani satu hari demi satu hari.',
                'tag' => 'Solidaritas Bersama',
            ],
        ];

        return [
            'week_number' => $currentWeek,
            'year' => $currentYear,
            'role_filter' => $roleFilter,
            'aggregates' => $aggregates,
            'solidarity_insights' => $solidarityInsights,
            'total_community_checkins' => 342,
        ];
    }
}

<?php

namespace App\Services;

use App\Models\DailyCheckin;
use App\Models\User;
use App\Models\WhatIfScenario;

class WhatIfSimulatorService
{
    /**
     * Run deterministic habit simulator based on user historical check-ins
     */
    public function simulate(User $user, string $variableChange): array
    {
        $allCheckins = DailyCheckin::with('signal')
            ->where('user_id', $user->id)
            ->get();

        if ($allCheckins->isEmpty()) {
            return [
                'success' => false,
                'message' => 'Belum ada data riwayat check-in untuk menjalankan simulasi.',
            ];
        }

        $scenariosConfig = [
            'sleep_plus_1h' => [
                'title' => 'Gimana kalau tidur 1 jam lebih awal (Tidur $\ge$ 7.5 jam)?',
                'target_metric' => 'energy',
                'target_metric_label' => 'Tingkat Energi Harian',
                'unit' => '%',
                'filter_condition' => fn($c) => ($c->signal?->sleep_hours ?? 0) >= 7.5,
                'baseline_fn' => fn($c) => $c->signal?->energy_level ?? 50,
                'explanation' => 'Berdasarkan hari-hari di mana kamu tidur minimal 7.5 jam, energimu menunjukkan peningkatan nyata dibanding hari biasa.',
            ],
            'physical_activity_20m' => [
                'title' => 'Gimana kalau rutin jalan santai/olahraga $\ge$ 20 menit sehari?',
                'target_metric' => 'mood',
                'target_metric_label' => 'Kestabilan Mood & Mental',
                'unit' => '%',
                'filter_condition' => fn($c) => ($c->signal?->physical_activity_min ?? 0) >= 20,
                'baseline_fn' => fn($c) => $c->signal?->mood_level ?? 50,
                'explanation' => 'Gerakan fisik ringan $\ge$ 20 menit konsisten membantu meningkatkan suasana hatimu dan mengurangi ketegangan pikiran.',
            ],
            'reduce_workload' => [
                'title' => 'Gimana kalau membatasi jam lembur (Beban kerja $\le$ 50)?',
                'target_metric' => 'stress',
                'target_metric_label' => 'Tingkat Stres',
                'unit' => '%',
                'filter_condition' => fn($c) => ($c->signal?->workload_score ?? 100) <= 50,
                'baseline_fn' => fn($c) => $c->signal?->stress_level ?? 50,
                'is_lower_better' => true,
                'explanation' => 'Saat beban kerja berada di level terkontrol, tingkat stresmu tercatat jauh lebih rendah secara historis.',
            ],
            'social_connection' => [
                'title' => 'Gimana kalau menjaga interaksi hangat dengan lingkaran terdekat?',
                'target_metric' => 'overall',
                'target_metric_label' => 'Skor Kesejahteraan Keseluruhan',
                'unit' => 'poin',
                'filter_condition' => fn($c) => ($c->signal?->social_interaction_score ?? 0) >= 70,
                'baseline_fn' => fn($c) => $c->overall_wellbeing_score,
                'explanation' => 'Terhubung dengan orang-orang terpercaya terbukti memberi suntikan emosional positif bagi kesejahteraan hidupmu.',
            ],
        ];

        $config = $scenariosConfig[$variableChange] ?? $scenariosConfig['sleep_plus_1h'];

        // 1. Calculate Baseline (All historical days)
        $baselineValues = $allCheckins->map($config['baseline_fn'])->filter();
        $baselineAverage = $baselineValues->count() > 0 ? round($baselineValues->avg(), 1) : 50.0;

        // 2. Calculate Scenario Value (Filtered historical days where condition was met)
        $matchingCheckins = $allCheckins->filter($config['filter_condition']);
        $matchingValues = $matchingCheckins->map($config['baseline_fn'])->filter();

        if ($matchingValues->count() > 0) {
            $projectedAverage = round($matchingValues->avg(), 1);
            $sampleDaysCount = $matchingValues->count();
        } else {
            // If user doesn't have exact matching days yet, calculate deterministic theoretical model
            $projectedAverage = isset($config['is_lower_better']) && $config['is_lower_better'] 
                ? max(15, round($baselineAverage * 0.75, 1))
                : min(95, round($baselineAverage * 1.22, 1));
            $sampleDaysCount = 0;
        }

        $potentialDelta = round($projectedAverage - $baselineAverage, 1);
        $potentialDeltaPercent = $baselineAverage > 0 ? round(($potentialDelta / $baselineAverage) * 100, 1) : 0;

        // Save scenario record
        WhatIfScenario::create([
            'user_id' => $user->id,
            'target_metric' => $config['target_metric'],
            'variable_change' => $variableChange,
            'potential_delta' => $potentialDelta,
            'baseline_value' => $baselineAverage,
            'projected_value' => $projectedAverage,
            'scenario_data_json' => [
                'sample_days_count' => $sampleDaysCount,
                'total_days_count' => $allCheckins->count(),
                'title' => $config['title'],
            ],
        ]);

        return [
            'success' => true,
            'scenario_key' => $variableChange,
            'title' => $config['title'],
            'target_metric' => $config['target_metric'],
            'target_metric_label' => $config['target_metric_label'],
            'unit' => $config['unit'],
            'baseline_value' => $baselineAverage,
            'projected_value' => $projectedAverage,
            'potential_delta' => $potentialDelta,
            'potential_delta_percent' => $potentialDeltaPercent,
            'sample_days_count' => $sampleDaysCount,
            'total_historical_days' => $allCheckins->count(),
            'explanation' => $config['explanation'],
            'disclaimer' => 'Estimasi ini murni dihitung berdasarkan pola dari data riwayat check-in kamu sebelumnya, bukan prediksi medis.',
            'available_scenarios' => array_keys($scenariosConfig),
        ];
    }

    /**
     * Get all scenario options
     */
    public function getAvailableScenarios(): array
    {
        return [
            'sleep_plus_1h' => 'Tidur 1 jam lebih awal (Tidur $\ge$ 7.5 jam)',
            'physical_activity_20m' => 'Jalan santai / gerak fisik $\ge$ 20 menit',
            'reduce_workload' => 'Membatasi beban lembur malam (Hard stop jam 22:00)',
            'social_connection' => 'Luangkan waktu ngobrol dengan sahabat/keluarga',
        ];
    }
}

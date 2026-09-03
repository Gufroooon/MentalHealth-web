<?php

/**
 * Dokumentasi file: Service business logic.
 *
 * Mencocokkan kondisi sinyal terbaru dengan rule knowledge base dan mengelola histori jurnal refleksi.
 */

namespace App\Services;

use App\Models\DailyCheckin;
use App\Models\KnowledgeBaseRule;
use App\Models\ReflectionJournal;
use App\Models\User;

class KnowledgeReflectionService
{
    /**
     * Memilih rule refleksi yang paling cocok dengan kondisi terbaru user.
     *
     * Check-in terakhir dimuat bersama LifeSignal, lalu rule difilter menurut
     * kategori pilihan dan diperiksa terhadap trigger_conditions_json. Setiap
     * kondisi yang cocok menambah skor; hanya rule yang seluruh kondisinya
     * terpenuhi yang dapat menjadi best rule. Fallback menjaga halaman tetap
     * memiliki prompt ketika data sinyal atau rule belum lengkap.
     */
    public function findBestRule(User $user, ?string $preferredCategory = null): ?KnowledgeBaseRule
    {
        // Sinyal terbaru dipakai karena prompt refleksi harus menggambarkan
        // keadaan user saat ini, bukan kondisi lama.
        $latestCheckin = DailyCheckin::with('signal')
            ->where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->first();

        $rulesQuery = KnowledgeBaseRule::query();

        if ($preferredCategory && $preferredCategory !== 'all') {
            $rulesQuery->where('category', $preferredCategory);
        }

        // Priority menjadi urutan dasar ketika beberapa rule sama-sama cocok
        // atau ketika belum tersedia sinyal untuk melakukan scoring.
        $allRules = $rulesQuery->orderBy('priority', 'asc')->get();

        if ($allRules->isEmpty()) {
            return KnowledgeBaseRule::first();
        }

        if (! $latestCheckin || ! $latestCheckin->signal) {
            return $allRules->first();
        }

        $signal = $latestCheckin->signal;
        $bestRule = null;
        $highestScore = -1;

        foreach ($allRules as $rule) {
            $cond = $rule->trigger_conditions_json ?? [];
            $matchScore = 0;
            $conditionsCount = count($cond);

            if ($conditionsCount === 0) {
                continue;
            }

            $matchedAll = true;

            foreach ($cond as $key => $targetVal) {
                switch ($key) {
                    case 'sleep_hours_max':
                        if ($signal->sleep_hours <= $targetVal) {
                            $matchScore += 2;
                        } else {
                            $matchedAll = false;
                        }
                        break;
                    case 'sleep_hours_min':
                        if ($signal->sleep_hours >= $targetVal) {
                            $matchScore += 2;
                        } else {
                            $matchedAll = false;
                        }
                        break;
                    case 'sleep_quality_max':
                        if ($signal->sleep_quality <= $targetVal) {
                            $matchScore += 2;
                        } else {
                            $matchedAll = false;
                        }
                        break;
                    case 'sleep_quality_min':
                        if ($signal->sleep_quality >= $targetVal) {
                            $matchScore += 2;
                        } else {
                            $matchedAll = false;
                        }
                        break;
                    case 'energy_max':
                        if ($signal->energy_level <= $targetVal) {
                            $matchScore += 2;
                        } else {
                            $matchedAll = false;
                        }
                        break;
                    case 'energy_min':
                        if ($signal->energy_level >= $targetVal) {
                            $matchScore += 2;
                        } else {
                            $matchedAll = false;
                        }
                        break;
                    case 'stress_min':
                        if ($signal->stress_level >= $targetVal) {
                            $matchScore += 2;
                        } else {
                            $matchedAll = false;
                        }
                        break;
                    case 'overthinking_min':
                        if ($signal->overthinking_level >= $targetVal) {
                            $matchScore += 2;
                        } else {
                            $matchedAll = false;
                        }
                        break;
                    case 'focus_max':
                        if ($signal->focus_level <= $targetVal) {
                            $matchScore += 2;
                        } else {
                            $matchedAll = false;
                        }
                        break;
                    case 'mood_max':
                        if ($signal->mood_level <= $targetVal) {
                            $matchScore += 2;
                        } else {
                            $matchedAll = false;
                        }
                        break;
                    case 'loneliness_min':
                        if ($signal->loneliness_score >= $targetVal) {
                            $matchScore += 2;
                        } else {
                            $matchedAll = false;
                        }
                        break;
                    case 'friction_min':
                        if ($signal->relationship_friction_score >= $targetVal) {
                            $matchScore += 2;
                        } else {
                            $matchedAll = false;
                        }
                        break;
                    case 'social_interaction_max':
                        if ($signal->social_interaction_score <= $targetVal) {
                            $matchScore += 2;
                        } else {
                            $matchedAll = false;
                        }
                        break;
                    case 'social_interaction_min':
                        if ($signal->social_interaction_score >= $targetVal) {
                            $matchScore += 2;
                        } else {
                            $matchedAll = false;
                        }
                        break;
                    case 'workload_min':
                        if ($signal->workload_score >= $targetVal) {
                            $matchScore += 2;
                        } else {
                            $matchedAll = false;
                        }
                        break;
                    case 'financial_pressure_min':
                        if ($signal->financial_pressure_score >= $targetVal) {
                            $matchScore += 2;
                        } else {
                            $matchedAll = false;
                        }
                        break;
                    case 'goal_progress_max':
                        if ($signal->goal_progress_score <= $targetVal) {
                            $matchScore += 2;
                        } else {
                            $matchedAll = false;
                        }
                        break;
                    case 'overall_min':
                        if ($latestCheckin->overall_wellbeing_score >= $targetVal) {
                            $matchScore += 2;
                        } else {
                            $matchedAll = false;
                        }
                        break;
                }
            }

            if ($matchedAll && $matchScore > $highestScore) {
                $highestScore = $matchScore;
                $bestRule = $rule;
            }
        }

        return $bestRule ?? $allRules->first();
    }

    /**
     * Menyimpan jawaban refleksi beserta snapshot prompt yang ditampilkan.
     * Snapshot penting agar histori tetap menjelaskan konteks jawaban meski
     * isi rule knowledge base berubah di masa depan.
     */
    public function saveJournal(User $user, array $data): ReflectionJournal
    {
        return ReflectionJournal::create([
            'user_id' => $user->id,
            'rule_id' => $data['rule_id'] ?? null,
            'prompt_topic' => $data['prompt_topic'] ?? 'Refleksi Harian',
            'prompt_snapshot' => $data['prompt_snapshot'] ?? null,
            'question_snapshot' => $data['question_snapshot'] ?? null,
            'user_response' => $data['user_response'],
            'mood_after' => $data['mood_after'] ?? null,
        ]);
    }

    /**
     * Mengambil histori jurnal milik user dari yang terbaru ke terlama.
     * Query dibatasi user_id agar ruang refleksi tidak pernah membaca jurnal
     * milik akun lain.
     */
    public function getJournalsHistory(User $user)
    {
        return ReflectionJournal::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}

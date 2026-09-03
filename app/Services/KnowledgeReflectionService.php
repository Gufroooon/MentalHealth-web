<?php

namespace App\Services;

use App\Models\DailyCheckin;
use App\Models\KnowledgeBaseRule;
use App\Models\ReflectionJournal;
use App\Models\User;

class KnowledgeReflectionService
{
    /**
     * Match knowledge base rule based on user's current life signals or selected category
     */
    public function findBestRule(User $user, ?string $preferredCategory = null): ?KnowledgeBaseRule
    {
        $latestCheckin = DailyCheckin::with('signal')
            ->where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->first();

        $rulesQuery = KnowledgeBaseRule::query();

        if ($preferredCategory && $preferredCategory !== 'all') {
            $rulesQuery->where('category', $preferredCategory);
        }

        $allRules = $rulesQuery->orderBy('priority', 'asc')->get();

        if ($allRules->isEmpty()) {
            return KnowledgeBaseRule::first();
        }

        if (!$latestCheckin || !$latestCheckin->signal) {
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
                        if ($signal->sleep_hours <= $targetVal) $matchScore += 2;
                        else $matchedAll = false;
                        break;
                    case 'sleep_hours_min':
                        if ($signal->sleep_hours >= $targetVal) $matchScore += 2;
                        else $matchedAll = false;
                        break;
                    case 'sleep_quality_max':
                        if ($signal->sleep_quality <= $targetVal) $matchScore += 2;
                        else $matchedAll = false;
                        break;
                    case 'sleep_quality_min':
                        if ($signal->sleep_quality >= $targetVal) $matchScore += 2;
                        else $matchedAll = false;
                        break;
                    case 'energy_max':
                        if ($signal->energy_level <= $targetVal) $matchScore += 2;
                        else $matchedAll = false;
                        break;
                    case 'energy_min':
                        if ($signal->energy_level >= $targetVal) $matchScore += 2;
                        else $matchedAll = false;
                        break;
                    case 'stress_min':
                        if ($signal->stress_level >= $targetVal) $matchScore += 2;
                        else $matchedAll = false;
                        break;
                    case 'overthinking_min':
                        if ($signal->overthinking_level >= $targetVal) $matchScore += 2;
                        else $matchedAll = false;
                        break;
                    case 'focus_max':
                        if ($signal->focus_level <= $targetVal) $matchScore += 2;
                        else $matchedAll = false;
                        break;
                    case 'mood_max':
                        if ($signal->mood_level <= $targetVal) $matchScore += 2;
                        else $matchedAll = false;
                        break;
                    case 'loneliness_min':
                        if ($signal->loneliness_score >= $targetVal) $matchScore += 2;
                        else $matchedAll = false;
                        break;
                    case 'friction_min':
                        if ($signal->relationship_friction_score >= $targetVal) $matchScore += 2;
                        else $matchedAll = false;
                        break;
                    case 'social_interaction_max':
                        if ($signal->social_interaction_score <= $targetVal) $matchScore += 2;
                        else $matchedAll = false;
                        break;
                    case 'social_interaction_min':
                        if ($signal->social_interaction_score >= $targetVal) $matchScore += 2;
                        else $matchedAll = false;
                        break;
                    case 'workload_min':
                        if ($signal->workload_score >= $targetVal) $matchScore += 2;
                        else $matchedAll = false;
                        break;
                    case 'financial_pressure_min':
                        if ($signal->financial_pressure_score >= $targetVal) $matchScore += 2;
                        else $matchedAll = false;
                        break;
                    case 'goal_progress_max':
                        if ($signal->goal_progress_score <= $targetVal) $matchScore += 2;
                        else $matchedAll = false;
                        break;
                    case 'overall_min':
                        if ($latestCheckin->overall_wellbeing_score >= $targetVal) $matchScore += 2;
                        else $matchedAll = false;
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
     * Save user reflection journal response
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
     * Get user reflection journals history
     */
    public function getJournalsHistory(User $user)
    {
        return ReflectionJournal::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}

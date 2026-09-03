<?php

namespace App\Services;

use App\Models\RecoveryActivity;
use App\Models\RecoverySession;
use App\Models\User;

class RecoveryService
{
    /**
     * Get user's dynamic recovery profile ranking activities by average efficacy
     */
    public function getRecoveryProfile(User $user): array
    {
        $activities = RecoveryActivity::with(['sessions' => function ($q) use ($user) {
            $q->where('user_id', $user->id);
        }])->get();

        $rankedActivities = [];

        foreach ($activities as $act) {
            $sessions = $act->sessions;
            $count = $sessions->count();

            if ($count > 0) {
                $avgEnergyDelta = round($sessions->avg(fn($s) => $s->energy_after - $s->energy_before), 1);
                $avgMoodDelta = round($sessions->avg(fn($s) => $s->mood_after - $s->mood_before), 1);
                $totalImpactScore = round(($avgEnergyDelta * 0.5) + ($avgMoodDelta * 0.5), 1);
            } else {
                $avgEnergyDelta = null;
                $avgMoodDelta = null;
                $totalImpactScore = 0;
            }

            $rankedActivities[] = [
                'id' => $act->id,
                'name' => $act->name,
                'icon' => $act->icon,
                'category' => $act->category,
                'description' => $act->description,
                'default_duration_min' => $act->default_duration_min,
                'sessions_count' => $count,
                'avg_energy_delta' => $avgEnergyDelta,
                'avg_mood_delta' => $avgMoodDelta,
                'total_impact_score' => $totalImpactScore,
            ];
        }

        // Sort by total impact score descending, then sessions count
        usort($rankedActivities, function ($a, $b) {
            if ($b['total_impact_score'] == $a['total_impact_score']) {
                return $b['sessions_count'] <=> $a['sessions_count'];
            }
            return $b['total_impact_score'] <=> $a['total_impact_score'];
        });

        // Recent sessions
        $recentSessions = RecoverySession::with('activity')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return [
            'ranked_activities' => $rankedActivities,
            'recent_sessions' => $recentSessions,
            'total_sessions_count' => RecoverySession::where('user_id', $user->id)->count(),
        ];
    }

    /**
     * Record a new recovery session
     */
    public function logSession(User $user, array $data): RecoverySession
    {
        return RecoverySession::create([
            'user_id' => $user->id,
            'activity_id' => $data['activity_id'],
            'energy_before' => (int) $data['energy_before'],
            'energy_after' => (int) $data['energy_after'],
            'mood_before' => (int) $data['mood_before'],
            'mood_after' => (int) $data['mood_after'],
            'duration_minutes' => (int) ($data['duration_minutes'] ?? 15),
            'notes' => $data['notes'] ?? null,
        ]);
    }
}

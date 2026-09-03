<?php

/**
 * Dokumentasi file: Model Eloquent domain.
 *
 * Menjelaskan tanggung jawab file app/Models/LifeSignal.php serta hubungan data atau UI-nya dengan bagian aplikasi lain.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LifeSignal extends Model
{
    use HasFactory;

    protected $fillable = [
        'checkin_id',
        'user_id',
        'sleep_hours',
        'sleep_quality',
        'physical_activity_min',
        'energy_level',
        'stress_level',
        'focus_level',
        'overthinking_level',
        'mood_level',
        'social_interaction_score',
        'loneliness_score',
        'relationship_friction_score',
        'workload_score',
        'financial_pressure_score',
        'goal_progress_score',
    ];

    protected $casts = [
        'sleep_hours' => 'float',
        'sleep_quality' => 'integer',
        'physical_activity_min' => 'integer',
        'energy_level' => 'integer',
        'stress_level' => 'integer',
        'focus_level' => 'integer',
        'overthinking_level' => 'integer',
        'mood_level' => 'integer',
        'social_interaction_score' => 'integer',
        'loneliness_score' => 'integer',
        'relationship_friction_score' => 'integer',
        'workload_score' => 'integer',
        'financial_pressure_score' => 'integer',
        'goal_progress_score' => 'integer',
    ];

    /**
     * Relationship belongsTo: record ini dimiliki satu User melalui user_id. Relasi ini memastikan data domain selalu dapat ditelusuri kembali ke pemilik akun.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship belongsTo: LifeSignal berasal dari satu DailyCheckin melalui checkin_id. Detail signal dipakai bersama skor ringkas check-in.
     */
    public function checkin(): BelongsTo
    {
        return $this->belongsTo(DailyCheckin::class, 'checkin_id');
    }
}

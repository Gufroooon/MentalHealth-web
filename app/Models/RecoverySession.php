<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecoverySession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'activity_id',
        'energy_before',
        'energy_after',
        'mood_before',
        'mood_after',
        'duration_minutes',
        'notes',
    ];

    protected $casts = [
        'energy_before' => 'integer',
        'energy_after' => 'integer',
        'mood_before' => 'integer',
        'mood_after' => 'integer',
        'duration_minutes' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(RecoveryActivity::class, 'activity_id');
    }

    public function getEnergyDeltaAttribute(): int
    {
        return $this->energy_after - $this->energy_before;
    }

    public function getMoodDeltaAttribute(): int
    {
        return $this->mood_after - $this->mood_before;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DailyCheckin extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'mind_score',
        'body_score',
        'social_score',
        'life_score',
        'overall_wellbeing_score',
        'notes',
        'primary_tag',
    ];

    protected $casts = [
        'date' => 'date',
        'mind_score' => 'float',
        'body_score' => 'float',
        'social_score' => 'float',
        'life_score' => 'float',
        'overall_wellbeing_score' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function signal(): HasOne
    {
        return $this->hasOne(LifeSignal::class, 'checkin_id');
    }
}

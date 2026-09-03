<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatIfScenario extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'target_metric',
        'variable_change',
        'potential_delta',
        'baseline_value',
        'projected_value',
        'scenario_data_json',
    ];

    protected $casts = [
        'potential_delta' => 'float',
        'baseline_value' => 'float',
        'projected_value' => 'float',
        'scenario_data_json' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

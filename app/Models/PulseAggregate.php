<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PulseAggregate extends Model
{
    use HasFactory;

    protected $fillable = [
        'week_number',
        'year',
        'role_filter',
        'metric_name',
        'aggregate_value',
        'sample_count',
        'meta_json',
    ];

    protected $casts = [
        'week_number' => 'integer',
        'year' => 'integer',
        'aggregate_value' => 'float',
        'sample_count' => 'integer',
        'meta_json' => 'array',
    ];
}

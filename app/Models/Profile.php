<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status_role',
        'birth_date',
        'participate_pulse',
        'settings_json',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'participate_pulse' => 'boolean',
        'settings_json' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

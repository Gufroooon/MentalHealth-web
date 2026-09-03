<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RecoveryActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'icon',
        'category',
        'description',
        'default_duration_min',
    ];

    public function sessions(): HasMany
    {
        return $this->hasMany(RecoverySession::class, 'activity_id');
    }
}

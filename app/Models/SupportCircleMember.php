<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportCircleMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'circle_id',
        'name',
        'email',
        'phone',
        'relationship_type',
        'is_active',
        'last_pinged_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_pinged_at' => 'datetime',
    ];

    public function circle(): BelongsTo
    {
        return $this->belongsTo(SupportCircle::class, 'circle_id');
    }
}

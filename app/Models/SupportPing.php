<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportPing extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'support_type',
        'custom_short_note',
        'recipients_count',
    ];

    protected $casts = [
        'recipients_count' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

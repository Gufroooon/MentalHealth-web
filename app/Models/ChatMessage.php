<?php

/**
 * Dokumentasi file: Model Eloquent domain.
 *
 * Menjelaskan tanggung jawab file app/Models/ChatMessage.php serta hubungan data atau UI-nya dengan bagian aplikasi lain.
 */
// test

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sender',
        'message',
        'quick_replies_json',
        'intent_detected',
    ];

    protected $casts = [
        'quick_replies_json' => 'array',
    ];

    /**
     * Relationship belongsTo: record ini dimiliki satu User melalui user_id. Relasi ini memastikan data domain selalu dapat ditelusuri kembali ke pemilik akun.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

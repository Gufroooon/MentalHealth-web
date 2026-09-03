<?php

/**
 * Dokumentasi file: Model Eloquent domain.
 *
 * Menjelaskan tanggung jawab file app/Models/InsightPattern.php serta hubungan data atau UI-nya dengan bagian aplikasi lain.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InsightPattern extends Model
{
    use HasFactory;

    protected $table = 'insights_patterns';

    protected $fillable = [
        'user_id',
        'pattern_type',
        'title',
        'description_json',
        'detected_at',
        'status',
    ];

    protected $casts = [
        'description_json' => 'array',
        'detected_at' => 'datetime',
    ];

    /**
     * Relationship belongsTo: record ini dimiliki satu User melalui user_id. Relasi ini memastikan data domain selalu dapat ditelusuri kembali ke pemilik akun.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

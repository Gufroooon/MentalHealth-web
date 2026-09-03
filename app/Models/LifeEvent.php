<?php

/**
 * Dokumentasi file: Model Eloquent domain.
 *
 * Menjelaskan tanggung jawab file app/Models/LifeEvent.php serta hubungan data atau UI-nya dengan bagian aplikasi lain.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LifeEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'category',
        'start_date',
        'end_date',
        'severity_impact',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'severity_impact' => 'integer',
    ];

    /**
     * Relationship belongsTo: record ini dimiliki satu User melalui user_id. Relasi ini memastikan data domain selalu dapat ditelusuri kembali ke pemilik akun.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

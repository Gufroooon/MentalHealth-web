<?php

/**
 * Dokumentasi file: Model Eloquent domain.
 *
 * Menjelaskan tanggung jawab file app/Models/RecoverySession.php serta hubungan data atau UI-nya dengan bagian aplikasi lain.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecoverySession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'activity_id',
        'energy_before',
        'energy_after',
        'mood_before',
        'mood_after',
        'duration_minutes',
        'notes',
    ];

    protected $casts = [
        'energy_before' => 'integer',
        'energy_after' => 'integer',
        'mood_before' => 'integer',
        'mood_after' => 'integer',
        'duration_minutes' => 'integer',
    ];

    /**
     * Relationship belongsTo: record ini dimiliki satu User melalui user_id. Relasi ini memastikan data domain selalu dapat ditelusuri kembali ke pemilik akun.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship belongsTo: sesi recovery mengacu pada satu aktivitas katalog. Nama dan deskripsi aktivitas ditampilkan bersama hasil sesi.
     */
    public function activity(): BelongsTo
    {
        return $this->belongsTo(RecoveryActivity::class, 'activity_id');
    }

    public function getEnergyDeltaAttribute(): int
    {
        return $this->energy_after - $this->energy_before;
    }

    public function getMoodDeltaAttribute(): int
    {
        return $this->mood_after - $this->mood_before;
    }
}

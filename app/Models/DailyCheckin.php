<?php

/**
 * Dokumentasi file: Model Eloquent domain.
 *
 * Menjelaskan tanggung jawab file app/Models/DailyCheckin.php serta hubungan data atau UI-nya dengan bagian aplikasi lain.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DailyCheckin extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'mind_score',
        'body_score',
        'social_score',
        'life_score',
        'overall_wellbeing_score',
        'notes',
        'primary_tag',
    ];

    protected $casts = [
        'date' => 'date',
        'mind_score' => 'float',
        'body_score' => 'float',
        'social_score' => 'float',
        'life_score' => 'float',
        'overall_wellbeing_score' => 'float',
    ];

    /**
     * Relationship belongsTo: record ini dimiliki satu User melalui user_id. Relasi ini memastikan data domain selalu dapat ditelusuri kembali ke pemilik akun.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship hasOne: DailyCheckin memiliki satu LifeSignal. Dengan eager loading, controller dan service dapat membaca metrik rinci tanpa query manual terpisah.
     */
    public function signal(): HasOne
    {
        return $this->hasOne(LifeSignal::class, 'checkin_id');
    }
}

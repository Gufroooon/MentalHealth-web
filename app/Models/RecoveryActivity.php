<?php

/**
 * Dokumentasi file: Model Eloquent domain.
 *
 * Menjelaskan tanggung jawab file app/Models/RecoveryActivity.php serta hubungan data atau UI-nya dengan bagian aplikasi lain.
 */

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

    /**
     * Relationship hasMany: aktivitas recovery memiliki banyak sesi yang pernah dicatat user. Data ini menjadi sumber statistik dampak aktivitas.
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(RecoverySession::class, 'activity_id');
    }
}

<?php

/**
 * Dokumentasi file: Model Eloquent domain.
 *
 * Menjelaskan tanggung jawab file app/Models/SupportCircleMember.php serta hubungan data atau UI-nya dengan bagian aplikasi lain.
 */

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

    /**
     * Relationship belongsTo: member berada di satu SupportCircle. Relasi ini dipakai untuk memeriksa ownership sebelum perubahan data.
     */
    public function circle(): BelongsTo
    {
        return $this->belongsTo(SupportCircle::class, 'circle_id');
    }
}

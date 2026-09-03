<?php

/**
 * Dokumentasi file: Model Eloquent domain.
 *
 * Menjelaskan tanggung jawab file app/Models/SupportCircle.php serta hubungan data atau UI-nya dengan bagian aplikasi lain.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportCircle extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'circle_name',
    ];

    /**
     * Relationship belongsTo: record ini dimiliki satu User melalui user_id. Relasi ini memastikan data domain selalu dapat ditelusuri kembali ke pemilik akun.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship hasMany: satu SupportCircle memiliki banyak kontak terpercaya. Foreign key circle membatasi member pada lingkaran yang benar.
     */
    public function members(): HasMany
    {
        return $this->hasMany(SupportCircleMember::class, 'circle_id');
    }
}

<?php

/**
 * Dokumentasi file: Model Eloquent domain.
 *
 * Menjelaskan tanggung jawab file app/Models/User.php serta hubungan data atau UI-nya dengan bagian aplikasi lain.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relationship satu-ke-satu dengan Profile milik user. Profile menyimpan preferensi dan status role yang melengkapi data autentikasi User.
     */
    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    public function checkins(): HasMany
    {
        return $this->hasMany(DailyCheckin::class);
    }

    public function lifeSignals(): HasMany
    {
        return $this->hasMany(LifeSignal::class);
    }

    public function lifeEvents(): HasMany
    {
        return $this->hasMany(LifeEvent::class);
    }

    public function recoverySessions(): HasMany
    {
        return $this->hasMany(RecoverySession::class);
    }

    public function insightPatterns(): HasMany
    {
        return $this->hasMany(InsightPattern::class);
    }

    public function whatIfScenarios(): HasMany
    {
        return $this->hasMany(WhatIfScenario::class);
    }

    public function supportCircles(): HasMany
    {
        return $this->hasMany(SupportCircle::class);
    }

    public function supportPings(): HasMany
    {
        return $this->hasMany(SupportPing::class);
    }

    public function reflectionJournals(): HasMany
    {
        return $this->hasMany(ReflectionJournal::class);
    }

    public function privacyLogs(): HasMany
    {
        return $this->hasMany(PrivacyLog::class);
    }

    public function microActionLogs(): HasMany
    {
        return $this->hasMany(MicroActionLog::class);
    }
}

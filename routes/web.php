<?php

use App\Http\Controllers\CheckinController;
use App\Http\Controllers\CircleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PatternController;
use App\Http\Controllers\PrivacyController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PulseController;
use App\Http\Controllers\RecoveryController;
use App\Http\Controllers\ReflectionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // 1. Dashboard & Life Signal Engine
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Check-in & Micro-actions
    Route::post('/checkin', [CheckinController::class, 'store'])->name('checkin.store');
    Route::post('/micro-action/{microAction}/toggle', [CheckinController::class, 'toggleMicroAction'])->name('micro-action.toggle');

    // 2 & 3 & 4. Pattern Engine & What-If Habit Simulator
    Route::get('/patterns', [PatternController::class, 'index'])->name('pattern.index');
    Route::post('/patterns/simulate', [PatternController::class, 'simulate'])->name('pattern.simulate');
    Route::post('/patterns/events', [PatternController::class, 'storeEvent'])->name('pattern.events.store');
    Route::delete('/patterns/events/{event}', [PatternController::class, 'destroyEvent'])->name('pattern.events.destroy');

    // 6. Recovery Lab & Profile
    Route::get('/recovery', [RecoveryController::class, 'index'])->name('recovery.index');
    Route::post('/recovery/sessions', [RecoveryController::class, 'storeSession'])->name('recovery.sessions.store');
    Route::post('/recovery/activities', [RecoveryController::class, 'storeActivity'])->name('recovery.activities.store');

    // 7. Pulse (Anonymous Community Trends)
    Route::get('/pulse', [PulseController::class, 'index'])->name('pulse.index');

    // 8. Circle (Support Network)
    Route::get('/circle', [CircleController::class, 'index'])->name('circle.index');
    Route::post('/circle/members', [CircleController::class, 'storeMember'])->name('circle.members.store');
    Route::delete('/circle/members/{member}', [CircleController::class, 'destroyMember'])->name('circle.members.destroy');
    Route::post('/circle/ping', [CircleController::class, 'sendPing'])->name('circle.ping');

    // 9. Knowledge-Based Reflection Assistant
    Route::get('/reflection', [ReflectionController::class, 'index'])->name('reflection.index');
    Route::post('/reflection', [ReflectionController::class, 'store'])->name('reflection.store');

    // 9.b Interactive Chatbot Assistant (Tanya NARA)
    Route::get('/chat', [\App\Http\Controllers\ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat/send', [\App\Http\Controllers\ChatController::class, 'send'])->name('chat.send');
    Route::post('/chat/clear', [\App\Http\Controllers\ChatController::class, 'clear'])->name('chat.clear');

    // 10. Privacy Center & Data Control
    Route::get('/privacy', [PrivacyController::class, 'index'])->name('privacy.index');
    Route::post('/privacy/toggle-pulse', [PrivacyController::class, 'togglePulse'])->name('privacy.toggle-pulse');
    Route::get('/privacy/export/json', [PrivacyController::class, 'exportJson'])->name('privacy.export.json');
    Route::get('/privacy/export/csv', [PrivacyController::class, 'exportCsv'])->name('privacy.export.csv');
    Route::delete('/privacy/checkins/{checkin}', [PrivacyController::class, 'deleteCheckin'])->name('privacy.checkin.delete');
    Route::post('/privacy/wipe', [PrivacyController::class, 'wipeData'])->name('privacy.wipe');

    // Default Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

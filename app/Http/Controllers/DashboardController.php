<?php

/**
 * Dokumentasi file: Controller HTTP.
 *
 * Menggabungkan data dari service analitik untuk dikirim ke view dashboard.
 */

namespace App\Http\Controllers;

use App\Services\LifeSignalService;
use App\Services\RecoveryService;
use App\Services\WhatChangedService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(
        protected LifeSignalService $lifeSignalService,
        protected WhatChangedService $whatChangedService,
        protected RecoveryService $recoveryService
    ) {}

    /**
     * Menyiapkan seluruh data yang dibutuhkan dashboard user.
     * Alurnya mengambil user terautentikasi, memuat check-in terbaru dan
     * histori chart, menjalankan analisis mingguan, memilih micro-action,
     * lalu meneruskan hasil service ke view dashboard.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // 1. Latest Check-in & Signals
        $latestCheckin = $this->lifeSignalService->getLatestCheckin($user);

        // 2. 14-day history for charts
        $signalHistory = $this->lifeSignalService->getSignalHistory($user, 14);

        // 3. What Changed 7-day delta analysis
        $weeklyAnalysis = $this->whatChangedService->analyzeWeeklyChanges($user);

        // 4. One Small Thing today's micro-action
        $microAction = $this->lifeSignalService->getTodayMicroAction($user, $latestCheckin);

        // 5. Lowest vector & breakdown
        $lowestVector = $latestCheckin ? $this->lifeSignalService->getLowestSignalVector($latestCheckin) : null;

        // 6. Recovery ranking preview
        $recoveryData = $this->recoveryService->getRecoveryProfile($user);

        return view('dashboard', compact(
            'user',
            'latestCheckin',
            'signalHistory',
            'weeklyAnalysis',
            'microAction',
            'lowestVector',
            'recoveryData'
        ));
    }
}

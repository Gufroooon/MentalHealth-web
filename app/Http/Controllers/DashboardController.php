<?php

namespace App\Http\Controllers;

use App\Services\LifeSignalService;
use App\Services\WhatChangedService;
use App\Services\RecoveryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(
        protected LifeSignalService $lifeSignalService,
        protected WhatChangedService $whatChangedService,
        protected RecoveryService $recoveryService
    ) {}

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

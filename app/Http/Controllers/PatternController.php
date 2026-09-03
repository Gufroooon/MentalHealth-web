<?php

namespace App\Http\Controllers;

use App\Models\LifeEvent;
use App\Services\LifeSignalService;
use App\Services\PatternDetectionEngine;
use App\Services\WhatIfSimulatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatternController extends Controller
{
    public function __construct(
        protected PatternDetectionEngine $patternEngine,
        protected WhatIfSimulatorService $whatIfSimulator,
        protected LifeSignalService $lifeSignalService
    ) {}

    public function index(Request $request)
    {
        $user = Auth::user();
        
        // 1. Detect patterns & timeline
        $patternData = $this->patternEngine->detectPatterns($user, 30);
        
        // 2. 14-day signal history
        $signalHistory = $this->lifeSignalService->getSignalHistory($user, 14);

        // 3. Default What If Simulation (sleep_plus_1h)
        $scenarioKey = $request->query('scenario', 'sleep_plus_1h');
        $simulationResult = $this->whatIfSimulator->simulate($user, $scenarioKey);
        $availableScenarios = $this->whatIfSimulator->getAvailableScenarios();

        return view('pattern.index', compact(
            'user',
            'patternData',
            'signalHistory',
            'simulationResult',
            'availableScenarios',
            'scenarioKey'
        ));
    }

    /**
     * Run simulation via AJAX or form
     */
    public function simulate(Request $request)
    {
        $request->validate([
            'variable_change' => 'required|string',
        ]);

        $user = Auth::user();
        $result = $this->whatIfSimulator->simulate($user, $request->variable_change);

        if ($request->wantsJson()) {
            return response()->json($result);
        }

        return redirect()->route('pattern.index', ['scenario' => $request->variable_change]);
    }

    /**
     * Store new life event
     */
    public function storeEvent(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|in:exam,deadline,work,relationship,financial,health,other',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'severity_impact' => 'required|integer|min:1|max:5',
            'notes' => 'nullable|string|max:500',
        ]);

        $validated['user_id'] = Auth::id();
        LifeEvent::create($validated);

        return redirect()->route('pattern.index')->with('success', 'Agenda hidup berhasil dicatat ke timeline NARA!');
    }

    /**
     * Delete life event
     */
    public function destroyEvent(LifeEvent $event)
    {
        if ($event->user_id !== Auth::id()) {
            abort(403);
        }

        $event->delete();
        return redirect()->route('pattern.index')->with('success', 'Agenda hidup berhasil dihapus.');
    }
}

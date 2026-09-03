<?php

namespace App\Http\Controllers;

use App\Models\DailyCheckin;
use App\Models\KnowledgeBaseRule;
use App\Services\KnowledgeReflectionService;
use App\Services\LifeSignalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReflectionController extends Controller
{
    public function __construct(
        protected KnowledgeReflectionService $reflectionService,
        protected LifeSignalService $lifeSignalService
    ) {}

    public function index(Request $request)
    {
        $user = Auth::user();
        $preferredCategory = $request->query('category', 'all');

        // 1. Matched rule via deterministic engine
        $matchedRule = $this->reflectionService->findBestRule($user, $preferredCategory);

        // 2. Latest check-in for context
        $latestCheckin = $this->lifeSignalService->getLatestCheckin($user);

        // 3. User's previous journals history
        $journalsHistory = $this->reflectionService->getJournalsHistory($user);

        // 4. All rules catalog for topic selection
        $allRules = KnowledgeBaseRule::orderBy('priority', 'asc')->get();

        return view('reflection.index', compact(
            'user',
            'matchedRule',
            'latestCheckin',
            'journalsHistory',
            'allRules',
            'preferredCategory'
        ));
    }

    /**
     * Save user's reflection journal
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'rule_id' => 'nullable|exists:knowledge_base_rules,id',
            'prompt_topic' => 'required|string|max:255',
            'prompt_snapshot' => 'nullable|string',
            'question_snapshot' => 'nullable|string',
            'user_response' => 'required|string|min:3|max:5000',
            'mood_after' => 'nullable|integer|min:0|max:100',
        ]);

        $user = Auth::user();
        $this->reflectionService->saveJournal($user, $validated);

        return redirect()->route('reflection.index')->with('success', 'Catatan refleksi berhasil disimpan ke ruang privatmu.');
    }
}

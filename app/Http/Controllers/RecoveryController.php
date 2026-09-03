<?php

namespace App\Http\Controllers;

use App\Models\RecoveryActivity;
use App\Services\RecoveryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecoveryController extends Controller
{
    public function __construct(
        protected RecoveryService $recoveryService
    ) {}

    public function index()
    {
        $user = Auth::user();
        $profileData = $this->recoveryService->getRecoveryProfile($user);
        $allActivities = RecoveryActivity::all();

        return view('recovery.index', compact('user', 'profileData', 'allActivities'));
    }

    /**
     * Log a new recovery session
     */
    public function storeSession(Request $request)
    {
        $validated = $request->validate([
            'activity_id' => 'required|exists:recovery_activities,id',
            'energy_before' => 'required|integer|min:0|max:100',
            'energy_after' => 'required|integer|min:0|max:100',
            'mood_before' => 'required|integer|min:0|max:100',
            'mood_after' => 'required|integer|min:0|max:100',
            'duration_minutes' => 'required|integer|min:1|max:300',
            'notes' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $this->recoveryService->logSession($user, $validated);

        return redirect()->route('recovery.index')->with('success', 'Eksperimen pemulihan berhasil dicatat! Profil pemulihanmu telah diperbarui.');
    }

    /**
     * Add new custom recovery activity
     */
    public function storeActivity(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|in:physical,mental,social,sensory,creative',
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:500',
            'default_duration_min' => 'required|integer|min:5|max:180',
        ]);

        RecoveryActivity::create($validated);

        return redirect()->route('recovery.index')->with('success', 'Aktivitas pemulihan baru berhasil ditambahkan ke katalog!');
    }
}

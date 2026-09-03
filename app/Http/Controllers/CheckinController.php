<?php

/**
 * Dokumentasi file: Controller HTTP.
 *
 * Menerima check-in dan perubahan status micro-action, lalu mengembalikan JSON atau redirect.
 */

namespace App\Http\Controllers;

use App\Models\MicroActionLog;
use App\Services\LifeSignalService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckinController extends Controller
{
    public function __construct(
        protected LifeSignalService $lifeSignalService
    ) {}

    /**
     * Menerima form check-in harian dan meneruskannya ke LifeSignalService.
     * Validasi memastikan semua vector berada pada rentang yang masuk akal;
     * service kemudian menghitung skor dan menyimpan ringkasan serta signal.
     * Request AJAX mendapat JSON, sedangkan form biasa kembali ke dashboard.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'nullable|date',

            // Mind Vector
            'mood_level' => 'required|numeric|min:0|max:100',
            'focus_level' => 'required|numeric|min:0|max:100',
            'stress_level' => 'required|numeric|min:0|max:100',
            'overthinking_level' => 'required|numeric|min:0|max:100',

            // Body Vector
            'sleep_hours' => 'required|numeric|min:0|max:24',
            'sleep_quality' => 'required|numeric|min:0|max:100',
            'energy_level' => 'required|numeric|min:0|max:100',
            'physical_activity_min' => 'required|numeric|min:0|max:480',

            // Social Vector
            'social_interaction_score' => 'required|numeric|min:0|max:100',
            'loneliness_score' => 'required|numeric|min:0|max:100',
            'relationship_friction_score' => 'required|numeric|min:0|max:100',

            // Life Vector
            'workload_score' => 'required|numeric|min:0|max:100',
            'financial_pressure_score' => 'required|numeric|min:0|max:100',
            'goal_progress_score' => 'required|numeric|min:0|max:100',

            'notes' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();
        $date = $validated['date'] ?? Carbon::today()->toDateString();

        $this->lifeSignalService->recordCheckin($user, $validated, $date);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Check-in hari ini berhasil dicatat!',
            ]);
        }

        return redirect()->route('dashboard')->with('success', 'Check-in hari ini berhasil dicatat!');
    }

    /**
     * Membalik status selesai micro-action milik user yang sedang login.
     * Pemeriksaan user_id mencegah route model binding dipakai untuk mengubah
     * aksi milik akun lain; timestamp hanya diisi ketika aksi selesai.
     */
    public function toggleMicroAction(Request $request, MicroActionLog $microAction)
    {
        if ($microAction->user_id !== Auth::id()) {
            abort(403);
        }

        $microAction->is_completed = ! $microAction->is_completed;
        $microAction->completed_at = $microAction->is_completed ? Carbon::now() : null;
        $microAction->save();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_completed' => $microAction->is_completed,
                'message' => $microAction->is_completed ? 'Hebat! Satu langkah kecil selesai hari ini.' : 'Status aksi diperbarui.',
            ]);
        }

        return back()->with('success', $microAction->is_completed ? 'Hebat! Satu langkah kecil selesai hari ini.' : 'Status aksi diperbarui.');
    }
}

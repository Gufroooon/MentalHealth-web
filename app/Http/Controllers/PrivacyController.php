<?php

namespace App\Http\Controllers;

use App\Models\DailyCheckin;
use App\Models\PrivacyLog;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PrivacyController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $profile = $user->profile ?? Profile::firstOrCreate(['user_id' => $user->id]);
        $checkins = DailyCheckin::where('user_id', $user->id)->orderBy('date', 'desc')->get();
        $privacyLogs = PrivacyLog::where('user_id', $user->id)->orderBy('created_at', 'desc')->take(10)->get();

        return view('privacy.index', compact('user', 'profile', 'checkins', 'privacyLogs'));
    }

    /**
     * Toggle anonymized Pulse participation
     */
    public function togglePulse(Request $request)
    {
        $user = Auth::user();
        $profile = $user->profile ?? Profile::firstOrCreate(['user_id' => $user->id]);
        
        $profile->participate_pulse = !$profile->participate_pulse;
        $profile->save();

        PrivacyLog::create([
            'user_id' => $user->id,
            'action_type' => 'toggle_pulse',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $statusText = $profile->participate_pulse ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('privacy.index')->with('success', "Partisipasi data anonim ke Tren Komunitas (Pulse) berhasil {$statusText}.");
    }

    /**
     * Export all user data as JSON
     */
    public function exportJson(Request $request)
    {
        $user = Auth::user();
        
        $data = [
            'app' => 'NARA - Life Pattern Companion',
            'exported_at' => now()->toIso8601String(),
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->profile?->status_role,
                'registered_at' => $user->created_at->toIso8601String(),
            ],
            'daily_checkins' => $user->checkins()->with('signal')->get(),
            'life_events' => $user->lifeEvents,
            'recovery_sessions' => $user->recoverySessions()->with('activity')->get(),
            'reflection_journals' => $user->reflectionJournals,
            'support_circle' => $user->supportCircles()->with('members')->get(),
            'insights' => $user->insightPatterns,
        ];

        PrivacyLog::create([
            'user_id' => $user->id,
            'action_type' => 'export_json',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $fileName = 'nara_data_export_' . date('Y_m_d_His') . '.json';
        return response()->json($data, 200, [
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }

    /**
     * Export checkins as CSV
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $user = Auth::user();

        PrivacyLog::create([
            'user_id' => $user->id,
            'action_type' => 'export_csv',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $fileName = 'nara_checkins_' . date('Y_m_d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ];

        return response()->stream(function () use ($user) {
            $handle = fopen('php://output', 'w');
            
            // CSV Header
            fputcsv($handle, [
                'Tanggal', 'Skor Keseluruhan', 'Mind Score', 'Body Score', 'Social Score', 'Life Score',
                'Jam Tidur', 'Kualitas Tidur', 'Energi', 'Stres', 'Fokus', 'Overthinking',
                'Interaksi Sosial', 'Kesepian', 'Beban Kerja', 'Tekanan Finansial', 'Catatan'
            ]);

            $checkins = DailyCheckin::with('signal')
                ->where('user_id', $user->id)
                ->orderBy('date', 'asc')
                ->get();

            foreach ($checkins as $c) {
                fputcsv($handle, [
                    $c->date->format('Y-m-d'),
                    $c->overall_wellbeing_score,
                    $c->mind_score,
                    $c->body_score,
                    $c->social_score,
                    $c->life_score,
                    $c->signal?->sleep_hours ?? '-',
                    $c->signal?->sleep_quality ?? '-',
                    $c->signal?->energy_level ?? '-',
                    $c->signal?->stress_level ?? '-',
                    $c->signal?->focus_level ?? '-',
                    $c->signal?->overthinking_level ?? '-',
                    $c->signal?->social_interaction_score ?? '-',
                    $c->signal?->loneliness_score ?? '-',
                    $c->signal?->workload_score ?? '-',
                    $c->signal?->financial_pressure_score ?? '-',
                    $c->notes ?? '',
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Delete individual check-in
     */
    public function deleteCheckin(Request $request, DailyCheckin $checkin)
    {
        if ($checkin->user_id !== Auth::id()) {
            abort(403);
        }

        $checkinDate = $checkin->date->format('d M Y');
        $checkin->delete();

        PrivacyLog::create([
            'user_id' => Auth::id(),
            'action_type' => 'delete_checkin',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('privacy.index')->with('success', "Catatan check-in tanggal {$checkinDate} berhasil dihapus secara permanen.");
    }

    /**
     * Complete Wipe Account & All Data
     */
    public function wipeData(Request $request)
    {
        $user = Auth::user();
        
        // Log wipe
        PrivacyLog::create([
            'user_id' => $user->id,
            'action_type' => 'wipe_account',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Delete all user related records
        $user->checkins()->delete();
        $user->lifeSignals()->delete();
        $user->lifeEvents()->delete();
        $user->recoverySessions()->delete();
        $user->insightPatterns()->delete();
        $user->whatIfScenarios()->delete();
        $user->supportCircles()->delete();
        $user->supportPings()->delete();
        $user->reflectionJournals()->delete();
        $user->microActionLogs()->delete();
        $user->profile()->delete();

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('status', 'Seluruh data dan akun NARA kamu telah dihapus secara permanen dari server.');
    }
}

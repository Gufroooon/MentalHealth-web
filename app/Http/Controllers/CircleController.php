<?php

/**
 * Dokumentasi file: Controller HTTP.
 *
 * Menjelaskan tanggung jawab file app/Http/Controllers/CircleController.php serta hubungan data atau UI-nya dengan bagian aplikasi lain.
 */

namespace App\Http\Controllers;

use App\Models\SupportCircle;
use App\Models\SupportCircleMember;
use App\Models\SupportPing;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CircleController extends Controller
{
    /**
     * Menyiapkan circle user, daftar member, dan sepuluh ping terbaru untuk UI.
     * firstOrCreate memastikan wadah circle tersedia sebelum data dibaca.
     */
    public function index()
    {
        $user = Auth::user();

        $circle = SupportCircle::firstOrCreate(
            ['user_id' => $user->id],
            ['circle_name' => 'Lingkaran Aman '.$user->name]
        );

        $members = $circle->members()->orderBy('created_at', 'desc')->get();
        $pingsHistory = SupportPing::where('user_id', $user->id)->orderBy('created_at', 'desc')->take(10)->get();

        return view('circle.index', compact('user', 'circle', 'members', 'pingsHistory'));
    }

    /**
     * Memvalidasi lalu menambahkan kontak terpercaya ke circle user.
     * Relationship circle->members otomatis mengisi foreign key circle.
     */
    public function storeMember(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'relationship_type' => 'required|string|in:sahabat,keluarga,pasangan,mentor,rekan,lainnya',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
        ]);

        $user = Auth::user();
        $circle = SupportCircle::firstOrCreate(['user_id' => $user->id]);

        $circle->members()->create($validated);

        return redirect()->route('circle.index')->with('success', 'Kontak terpercaya berhasil ditambahkan ke lingkaran amanmu!');
    }

    /**
     * Menghapus member setelah ownership circle diverifikasi agar kontak user
     * lain tidak dapat dihapus melalui route model binding.
     */
    public function destroyMember(SupportCircleMember $member)
    {
        if ($member->circle->user_id !== Auth::id()) {
            abort(403);
        }

        $member->delete();

        return redirect()->route('circle.index')->with('success', 'Kontak berhasil dihapus dari lingkaran.');
    }

    /**
     * Send privacy-first Ping Support ("Hari ini agak berat")
     */
    public function sendPing(Request $request)
    {
        $validated = $request->validate([
            'support_type' => 'required|string|in:general,vent,hangout,quiet_presence',
            'custom_short_note' => 'nullable|string|max:140',
        ]);

        $user = Auth::user();
        $circle = SupportCircle::where('user_id', $user->id)->first();
        $activeMembers = $circle ? $circle->members()->where('is_active', true)->get() : collect();

        $ping = SupportPing::create([
            'user_id' => $user->id,
            'support_type' => $validated['support_type'],
            'custom_short_note' => $validated['custom_short_note'] ?? null,
            'recipients_count' => $activeMembers->count(),
        ]);

        // Update last_pinged_at for members
        foreach ($activeMembers as $m) {
            $m->update(['last_pinged_at' => Carbon::now()]);
        }

        return redirect()->route('circle.index')->with('success', 'Sinyal support terkirim ke '.$activeMembers->count().' kontak terpercayamu secara privat dan aman.');
    }
}

<?php

/**
 * Dokumentasi file: Controller HTTP.
 *
 * Menjelaskan tanggung jawab file app/Http/Controllers/PulseController.php serta hubungan data atau UI-nya dengan bagian aplikasi lain.
 */

namespace App\Http\Controllers;

use App\Services\PulseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PulseController extends Controller
{
    public function __construct(
        protected PulseService $pulseService
    ) {}

    /**
     * Memilih filter role dari query string, mengambil agregasi anonim melalui
     * PulseService, lalu mengirim data tren ke view Pulse.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $roleFilter = $request->query('role', 'all');
        $pulseData = $this->pulseService->getCommunityPulse($roleFilter);

        return view('pulse.index', compact('user', 'pulseData', 'roleFilter'));
    }
}

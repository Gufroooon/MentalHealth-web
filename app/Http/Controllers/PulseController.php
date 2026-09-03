<?php

namespace App\Http\Controllers;

use App\Services\PulseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PulseController extends Controller
{
    public function __construct(
        protected PulseService $pulseService
    ) {}

    public function index(Request $request)
    {
        $user = Auth::user();
        $roleFilter = $request->query('role', 'all');
        $pulseData = $this->pulseService->getCommunityPulse($roleFilter);

        return view('pulse.index', compact('user', 'pulseData', 'roleFilter'));
    }
}

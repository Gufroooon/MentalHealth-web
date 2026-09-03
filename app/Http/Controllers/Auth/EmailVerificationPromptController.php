<?php

/**
 * Dokumentasi file: Controller HTTP.
 *
 * Menjelaskan tanggung jawab file app/Http/Controllers/Auth/EmailVerificationPromptController.php serta hubungan data atau UI-nya dengan bagian aplikasi lain.
 */

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        return $request->user()->hasVerifiedEmail()
                    ? redirect()->intended(route('dashboard', absolute: false))
                    : view('auth.verify-email');
    }
}

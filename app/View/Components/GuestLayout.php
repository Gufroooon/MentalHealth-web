<?php

/**
 * Dokumentasi file: Entry point aplikasi.
 *
 * Menjelaskan tanggung jawab file app/View/Components/GuestLayout.php serta hubungan data atau UI-nya dengan bagian aplikasi lain.
 */

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class GuestLayout extends Component
{
    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.guest');
    }
}

<?php

/**
 * Dokumentasi file: Entry point aplikasi.
 *
 * Menjelaskan tanggung jawab file app/View/Components/AppLayout.php serta hubungan data atau UI-nya dengan bagian aplikasi lain.
 */

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.app');
    }
}

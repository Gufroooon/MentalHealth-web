{{--
    Dokumentasi file: View Blade.

    Menjelaskan tanggung jawab file resources/views/layouts/guest.blade.php serta hubungan data atau UI-nya dengan bagian aplikasi lain.
--}}
<!DOCTYPE html>
<html lang="id" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'NARA') }}</title>

        <!-- Google Fonts: Plus Jakarta Sans -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts & Styles via Vite -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body { font-family: 'Plus Jakarta Sans', sans-serif; }
        </style>
    </head>
    <body class="min-h-screen bg-slate-50 text-slate-800 antialiased flex flex-col justify-center items-center p-4">
        <div class="w-full sm:max-w-md">
            
            <div class="text-center mb-6">
                <a href="/" class="inline-flex items-center gap-2.5">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-emerald-800 to-teal-600 flex items-center justify-center text-white font-bold text-2xl shadow-md shadow-emerald-800/20">
                        🌿
                    </div>
                </a>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight mt-2">NARA</h1>
                <p class="text-xs text-slate-500">Life Pattern Companion &bull; Teman Kesejahteraan Hidupmu</p>
            </div>

            <div class="bg-white rounded-3xl p-8 border border-slate-200/80 shadow-xl shadow-slate-200/50">
                {{ $slot }}
            </div>

            <div class="text-center mt-6 text-xs text-slate-400">
                &copy; {{ date('Y') }} NARA &bull; 100% Privat & Deterministik
            </div>
        </div>
    </body>
</html>

{{--
    Dokumentasi file: View Blade.

    Menjelaskan tanggung jawab file resources/views/layouts/app.blade.php serta hubungan data atau UI-nya dengan bagian aplikasi lain.
--}}
<!DOCTYPE html>
<html lang="id" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'NARA - Life Pattern Companion' }}</title>

        <!-- Google Fonts: Plus Jakarta Sans -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts & Styles via Vite -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            [x-cloak] { display: none !important; }
            body { font-family: 'Plus Jakarta Sans', sans-serif; }
        </style>
    </head>
    <body class="min-h-screen bg-slate-50 text-slate-800 antialiased flex flex-col selection:bg-emerald-100 selection:text-emerald-900">
        <div class="flex-1 flex flex-col">
            @include('layouts.navigation')

            <!-- Flash Alert Messages -->
            @if (session('success') || session('status'))
                <div x-data="{ show: true }" x-show="show" x-transition class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
                    <div class="bg-emerald-50 border border-emerald-200/80 rounded-2xl p-4 flex items-center justify-between text-emerald-800 shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <span class="text-sm font-medium">{{ session('success') ?? session('status') }}</span>
                        </div>
                        <button @click="show = false" class="text-emerald-500 hover:text-emerald-700 text-sm font-medium">✕</button>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div x-data="{ show: true }" x-show="show" x-transition class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
                    <div class="bg-rose-50 border border-rose-200/80 rounded-2xl p-4 text-rose-800 shadow-sm">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-semibold text-sm">Ada sedikit hal yang perlu diperiksa:</span>
                            <button @click="show = false" class="text-rose-500 hover:text-rose-700 text-sm font-medium">✕</button>
                        </div>
                        <ul class="list-disc list-inside text-xs space-y-1 text-rose-700">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white/80 backdrop-blur-md border-b border-slate-200/70 sticky top-0 z-10">
                    <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="flex-1 pb-16">
                {{ $slot }}
            </main>
        </div>

        <!-- Footer with Privacy Assurance -->
        <footer class="bg-white border-t border-slate-200/70 py-6 mt-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-lg bg-emerald-700 text-white flex items-center justify-center font-bold text-xs">N</div>
                    <span class="font-semibold text-slate-700">NARA</span>
                    <span>&bull;</span>
                    <span>A Personal Well-being System That Understands Your Life, Not Just Your Mood.</span>
                </div>
                <div class="flex items-center gap-4">
                    <a href="{{ route('privacy.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 hover:bg-emerald-50 text-slate-600 hover:text-emerald-700 transition">
                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        <span class="font-medium">100% Privat & Deterministik</span>
                    </a>
                    <span>&copy; {{ date('Y') }} NARA</span>
                </div>
            </div>
        </footer>

        <!-- Floating Interactive Chat Widget -->
        <x-floating-chat />

        @stack('scripts')
    </body>
</html>

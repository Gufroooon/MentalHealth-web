<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'NARA - Life Pattern Companion' }}</title>

        <!-- Google Fonts: Plus Jakarta Sans -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

        <!-- Scripts & Styles via Vite -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            [x-cloak] { display: none !important; }
            body { font-family: 'Plus Jakarta Sans', sans-serif; }
        </style>
    </head>
    <body class="bg-[#F4F7FB] text-slate-800 antialiased min-h-screen flex flex-col selection:bg-nara-200 selection:text-nara-900">
        <div class="flex-1 flex flex-col">
            @include('layouts.navigation')

            <!-- Flash Alert Messages -->
            @if (session('success') || session('status'))
                <div x-data="{ show: true }" x-show="show" x-transition class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
                    <div class="bg-white border border-emerald-200 rounded-2xl p-4 flex items-center justify-between text-emerald-900 shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                                <x-icon name="check" class="w-4 h-4 text-emerald-700" />
                            </div>
                            <span class="text-xs sm:text-sm font-bold">{{ session('success') ?? session('status') }}</span>
                        </div>
                        <button @click="show = false" aria-label="Tutup notifikasi" class="text-slate-400 hover:text-slate-600 text-lg leading-none font-bold">&times;</button>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div x-data="{ show: true }" x-show="show" x-transition class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
                    <div class="bg-white border border-rose-200 rounded-2xl p-4 text-rose-900 shadow-sm">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-black text-xs sm:text-sm text-rose-800">Ada hal yang perlu diperiksa:</span>
                            <button @click="show = false" aria-label="Tutup notifikasi" class="text-slate-400 hover:text-slate-600 text-lg leading-none font-bold">&times;</button>
                        </div>
                        <ul class="list-disc list-inside text-xs space-y-1 text-rose-700 font-medium">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Page Heading (Clean White Sub-Header) -->
            @isset($header)
                <header class="bg-white border-b border-slate-200/80 sticky top-16 z-20 shadow-xs">
                    <div class="max-w-7xl mx-auto py-4 sm:py-5 px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
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
        <footer class="bg-white border-t border-slate-200/80 py-6 mt-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500 font-medium">
                <div class="flex items-center gap-2.5">
                    <div class="w-6 h-6 rounded-lg bg-nara-600 text-white flex items-center justify-center font-black text-[10px]">N</div>
                    <span class="font-black text-slate-800">NARA</span>
                    <span>&bull;</span>
                    <span>Sistem Kesejahteraan Personal yang Memahami Pola Hidupmu.</span>
                </div>
                <div class="flex items-center gap-4">
                    <a href="{{ route('privacy.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-nara-50 hover:bg-nara-100 text-nara-800 transition font-bold border border-nara-200/60">
                        <x-icon name="privacy" class="w-3.5 h-3.5 text-nara-600" />
                        <span>100% Privat & Deterministik</span>
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

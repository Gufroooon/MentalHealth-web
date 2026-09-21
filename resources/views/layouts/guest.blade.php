<!DOCTYPE html>
<html lang="id" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'NARA') }} - Life Pattern Companion</title>

        <!-- Google Fonts: Plus Jakarta Sans -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

        <!-- Scripts & Styles via Vite -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body { font-family: 'Plus Jakarta Sans', sans-serif; }
        </style>
    </head>
    <body class="bg-[#F8F6F0] text-slate-800 antialiased min-h-screen flex items-center justify-center p-4 sm:p-6 lg:p-8 selection:bg-nara-200 selection:text-nara-900">
        <div class="w-full max-w-5xl">

            <div class="text-center mb-6 lg:hidden">
                <a href="/" class="inline-flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-2xl bg-nara-600 text-white flex items-center justify-center font-black text-xl shadow-sm">
                        N
                    </div>
                </a>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight mt-2">NARA</h1>
                <p class="text-xs text-slate-500 font-medium">Life Pattern Companion &bull; Teman Kesejahteraan Hidupmu</p>
            </div>

            <div class="grid lg:grid-cols-[0.95fr_1fr] gap-6 lg:gap-8 items-stretch">
                <!-- Left Editorial Panel -->
                <aside class="hidden lg:flex flex-col justify-between p-8 xl:p-10 rounded-3xl bg-gradient-to-br from-[#557FA8] via-[#4D769E] to-[#436B92] text-white shadow-hero border border-white/20 relative overflow-hidden">
                    <div class="absolute inset-0 hero-glow pointer-events-none"></div>

                    <div class="relative z-10">
                        <a href="/" class="inline-flex items-center gap-3 group">
                            <div class="w-10 h-10 rounded-2xl bg-white text-[#436B92] flex items-center justify-center font-black text-lg shadow-sm group-hover:scale-105 transition">
                                N
                            </div>
                            <div>
                                <span class="font-black text-xl tracking-tight text-white block leading-none">NARA</span>
                                <span class="text-[10px] font-bold tracking-widest uppercase text-white/70">Life Companion</span>
                            </div>
                        </a>

                        <div class="w-12 h-1 bg-white/30 rounded-full my-8"></div>

                        <span class="text-xs font-bold uppercase tracking-wider text-nara-100 bg-white/15 px-3 py-1 rounded-full border border-white/20 inline-block mb-3">
                            Ruang Memahami Diri
                        </span>
                        <h2 class="text-2xl xl:text-3xl font-extrabold leading-tight tracking-tight text-white">
                            Pelan-pelan juga tetap maju.
                        </h2>
                        <p class="mt-4 text-xs xl:text-sm leading-relaxed text-white/90 font-normal">
                            NARA membantu kamu melihat korelasi antara jam tidur, beban tugas, dan energimu dengan jernih &mdash; tanpa menghakimi dan tanpa membuat kesehatan mental terasa seperti beban tugas baru.
                        </p>
                    </div>

                    <div class="relative z-10 pt-6 border-t border-white/15 text-xs text-white/80 space-y-1">
                        <div class="flex items-center gap-2 font-bold text-white">
                            <x-icon name="privacy" class="w-4 h-4 text-emerald-300" />
                            <span>Privasi adalah bagian dari rasa aman.</span>
                        </div>
                        <p class="text-[11px] text-white/70">100% deterministik tanpa data keluar ke API AI pihak ketiga.</p>
                    </div>
                </aside>

                <!-- Right Form Card -->
                <main class="w-full max-w-md mx-auto lg:max-w-none flex items-center">
                    <div class="w-full bg-white rounded-3xl p-6 sm:p-8 lg:p-10 border border-slate-200/80 shadow-card">
                        {{ $slot }}
                    </div>
                </main>
            </div>

            <div class="text-center mt-6 text-xs text-slate-400 font-medium">
                &copy; {{ date('Y') }} NARA &bull; 100% Privat & Deterministik
            </div>
        </div>
    </body>
</html>

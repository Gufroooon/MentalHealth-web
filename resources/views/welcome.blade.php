<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>NARA - Life Pattern Companion | Sistem Kesejahteraan Personal</title>

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
    <body class="bg-[#F8F6F0] text-slate-800 antialiased min-h-screen flex flex-col selection:bg-nara-300 selection:text-nara-900">

        <!-- Main Wrapper Container with subtle frame inspired by reference -->
        <div class="w-full max-w-[1440px] mx-auto p-3 sm:p-6 lg:p-8 space-y-12">

            <!-- ========================================== -->
            <!-- HERO CONTAINER (Inspired by contohweb.jpg) -->
            <!-- ========================================== -->
            <header class="relative bg-gradient-to-b from-[#557FA8] via-[#4D769E] to-[#436B92] rounded-[2rem] sm:rounded-[2.5rem] lg:rounded-[3rem] text-white shadow-hero overflow-hidden border border-white/20">

                <!-- Ambient Glow & Subtle Background Organic Wave Graphics -->
                <div class="absolute inset-0 hero-glow pointer-events-none"></div>

                <!-- Top Organic Dots & Cross Accents -->
                <div class="absolute top-12 left-10 w-3 h-3 rounded-full bg-white/40 pointer-events-none"></div>
                <div class="absolute top-28 left-24 w-2 h-2 rounded-full bg-white/30 pointer-events-none"></div>
                <div class="absolute top-16 right-16 w-3.5 h-3.5 rounded-full bg-white/40 pointer-events-none"></div>
                <div class="absolute top-36 right-32 w-2 h-2 rounded-full bg-white/25 pointer-events-none"></div>

                <!-- Top Contained Navigation Bar -->
                <nav class="relative z-20 px-6 sm:px-10 lg:px-12 pt-6 sm:pt-8 pb-4 flex items-center justify-between border-b border-white/15">
                    <!-- Left: Profile Quick Link -->
                    <div class="flex items-center gap-2">
                        @auth
                            <a href="{{ route('dashboard') }}" class="w-10 h-10 rounded-full bg-white/20 hover:bg-white/30 border border-white/30 flex items-center justify-center text-white transition" title="Buka Dashboard">
                                <x-icon name="user" class="w-5 h-5 text-white" />
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="w-10 h-10 rounded-full bg-white/15 hover:bg-white/25 border border-white/25 flex items-center justify-center text-white transition" title="Masuk Akun">
                                <x-icon name="user" class="w-5 h-5 text-white" />
                            </a>
                        @endauth
                    </div>

                    <!-- Center-Left Links (Desktop) -->
                    <div class="hidden md:flex items-center space-x-8 text-sm font-semibold tracking-wide text-white/90">
                        <a href="#fitur" class="hover:text-white transition">4 Sinyal</a>
                        <a href="#modul" class="hover:text-white transition">10 Modul</a>
                    </div>

                    <!-- Center Brand Logo & Name -->
                    <a href="/" class="flex items-center gap-2.5 group">
                        <div class="w-9 h-9 rounded-2xl bg-white text-[#436B92] flex items-center justify-center font-extrabold text-lg shadow-sm group-hover:scale-105 transition">
                            N
                        </div>
                        <div class="text-left">
                            <span class="block text-lg font-black tracking-tight leading-none text-white">NARA</span>
                            <span class="block text-[9px] font-bold tracking-widest uppercase text-white/70">Life Companion</span>
                        </div>
                    </a>

                    <!-- Center-Right Links (Desktop) -->
                    <div class="hidden md:flex items-center space-x-8 text-sm font-semibold tracking-wide text-white/90">
                        <a href="#privasi" class="hover:text-white transition">Privasi</a>
                        <a href="{{ route('login') }}" class="hover:text-white transition">Demo</a>
                    </div>

                    <!-- Right Action Button -->
                    <div class="flex items-center gap-3">
                        @auth
                            <a href="{{ route('dashboard') }}" class="px-5 py-2.5 rounded-full bg-white text-[#2A4869] hover:bg-[#F8F6F0] font-bold text-xs shadow-pill transition transform hover:scale-105 active:scale-95">
                                Dashboard &rarr;
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="hidden sm:inline-block px-4 py-2 text-xs font-bold text-white/90 hover:text-white transition">
                                Masuk
                            </a>
                            <a href="{{ route('register') }}" class="px-5 py-2.5 rounded-full bg-white text-[#2A4869] hover:bg-[#F8F6F0] font-bold text-xs shadow-pill transition transform hover:scale-105 active:scale-95">
                                Mulai Sekarang
                            </a>
                        @endauth
                    </div>
                </nav>

                <!-- Hero Body: Human Left + Central Content + Human Right -->
                <div class="relative z-10 px-6 sm:px-10 lg:px-12 pt-10 pb-16 lg:pb-24 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">

                    <!-- LEFT HUMAN VISUAL (Desktop Col 3) -->
                    <div class="hidden lg:flex lg:col-span-3 flex-col items-center justify-center relative">
                        <div class="relative group">
                            <!-- Soft Organic Mask Container -->
                            <div class="w-60 h-72 rounded-[3.5rem] bg-gradient-to-br from-white/25 via-white/10 to-transparent p-2 backdrop-blur-xs border border-white/25 shadow-xl transition transform group-hover:scale-102">
                                <div class="w-full h-full rounded-[3.2rem] overflow-hidden relative bg-[#6890B4]/60 flex items-center justify-center">
                                    <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=600&q=80"
                                         alt="NARA - Kesejahteraan Pikiran"
                                         class="w-full h-full object-cover object-center transform transition duration-700 group-hover:scale-105 filter saturate-[0.9] brightness-[1.02]">
                                    <!-- Soft inner gradient mask -->
                                    <div class="absolute inset-0 bg-gradient-to-t from-[#436B92]/70 via-transparent to-transparent"></div>
                                </div>
                            </div>

                            <!-- Floating badge: Mind Harmony -->
                            <div class="absolute -bottom-4 left-1/2 transform -translate-x-1/2 px-4 py-2 rounded-full bg-white text-[#2A4869] shadow-lg border border-white/40 flex items-center gap-2 whitespace-nowrap text-xs font-bold">
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                                <span>Pikiran Tenang</span>
                            </div>

                            <!-- Botanical / Heart Organic Accent -->
                            <div class="absolute -top-4 -right-3 w-10 h-10 rounded-2xl bg-white/20 backdrop-blur-md border border-white/30 flex items-center justify-center text-white text-base shadow-md">
                                <x-icon name="heart" class="w-5 h-5 text-rose-200 fill-rose-200" />
                            </div>
                        </div>
                    </div>

                    <!-- CENTER CONTENT (Mobile: 12, Desktop: Col 6) -->
                    <div class="lg:col-span-6 text-center space-y-6 max-w-2xl mx-auto">

                        <!-- Tag Badge -->
                        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/15 border border-white/25 text-white text-xs font-semibold backdrop-blur-md shadow-xs">
                            <span class="w-2 h-2 rounded-full bg-emerald-300 animate-pulse"></span>
                            <span>100% Deterministik &bull; Tanpa External AI &bull; Privasi Utuh</span>
                        </div>

                        <!-- Main Headline -->
                        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white tracking-tight leading-[1.15]">
                            Sistem Kesejahteraan Personal yang Memahami <span class="underline decoration-white/50 decoration-wavy underline-offset-4 font-black">Pola Hidupmu</span>
                        </h1>

                        <!-- Supportive Subtext -->
                        <p class="text-sm sm:text-base text-white/90 leading-relaxed font-normal max-w-xl mx-auto">
                            NARA menghubungkan 4 vektor sinyal hidupmu (Pikiran, Tubuh, Sosial, dan Beban Hidup) dengan agenda harian. Menemukan sebab-akibat kelelahanmu dan merekomendasikan langkah mikro nyata untuk memulihkannya.
                        </p>

                        <!-- Quick Credentials Card & Primary Action -->
                        <div class="pt-2 max-w-md mx-auto space-y-3">
                            <div class="bg-white/15 backdrop-blur-md border border-white/25 rounded-2xl p-3 sm:p-4 text-xs text-white/90 flex flex-col sm:flex-row items-center justify-between gap-3 text-left">
                                <div class="space-y-0.5">
                                    <span class="font-bold text-white block text-xs">Akun Demo Siap Uji (14 Hari Data):</span>
                                    <div class="text-[11px] text-white/80 space-x-2 font-mono">
                                        <span>nara@wellbeing.id</span>
                                        <span>&bull;</span>
                                        <span>password</span>
                                    </div>
                                </div>
                                <a href="{{ route('login') }}" class="w-full sm:w-auto px-4 py-2 rounded-xl bg-white text-[#2A4869] hover:bg-[#F8F6F0] font-bold text-xs transition shadow-sm text-center flex-shrink-0">
                                    Masuk Demo &rarr;
                                </a>
                            </div>

                            <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-1">
                                <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-3.5 rounded-full bg-white text-[#2A4869] hover:bg-[#F8F6F0] font-black text-sm shadow-xl shadow-black/10 transition transform hover:-translate-y-0.5 text-center">
                                    Mulai Jelajahi NARA
                                </a>
                                <a href="#fitur" class="w-full sm:w-auto px-6 py-3.5 rounded-full bg-white/15 hover:bg-white/25 border border-white/30 text-white font-bold text-sm transition text-center">
                                    Pelajari 4 Vektor Sinyal
                                </a>
                            </div>
                        </div>

                    </div>

                    <!-- RIGHT HUMAN VISUAL (Desktop Col 3) -->
                    <div class="hidden lg:flex lg:col-span-3 flex-col items-center justify-center relative">
                        <div class="relative group">
                            <!-- Soft Organic Mask Container -->
                            <div class="w-60 h-72 rounded-[3.5rem] bg-gradient-to-bl from-white/25 via-white/10 to-transparent p-2 backdrop-blur-xs border border-white/25 shadow-xl transition transform group-hover:scale-102">
                                <div class="w-full h-full rounded-[3.2rem] overflow-hidden relative bg-[#6890B4]/60 flex items-center justify-center">
                                    <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=600&q=80"
                                         alt="NARA - Pemulihan Energi"
                                         class="w-full h-full object-cover object-center transform transition duration-700 group-hover:scale-105 filter saturate-[0.9] brightness-[1.02]">
                                    <!-- Soft inner gradient mask -->
                                    <div class="absolute inset-0 bg-gradient-to-t from-[#436B92]/70 via-transparent to-transparent"></div>
                                </div>
                            </div>

                            <!-- Floating badge: Energy Restored -->
                            <div class="absolute -bottom-4 left-1/2 transform -translate-x-1/2 px-4 py-2 rounded-full bg-white text-[#2A4869] shadow-lg border border-white/40 flex items-center gap-2 whitespace-nowrap text-xs font-bold">
                                <span class="w-2.5 h-2.5 rounded-full bg-teal-500"></span>
                                <span>Pemulihan Terukur</span>
                            </div>

                            <!-- Sparkle / Lotus Organic Accent -->
                            <div class="absolute -top-4 -left-3 w-10 h-10 rounded-2xl bg-white/20 backdrop-blur-md border border-white/30 flex items-center justify-center text-white text-base shadow-md">
                                <x-icon name="sparkle" class="w-5 h-5 text-amber-200 fill-amber-200" />
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Bottom Organic Decorative Wavy Lines & Shapes -->
                <div class="relative w-full overflow-hidden leading-none z-10 pointer-events-none opacity-40">
                    <svg class="w-full h-12 text-white" viewBox="0 0 1200 120" preserveAspectRatio="none" fill="currentColor">
                        <path d="M0,0 C150,90 350,-40 500,45 C650,130 900,10 1200,50 L1200,120 L0,120 Z"></path>
                    </svg>
                </div>

            </header>

            <!-- ========================================== -->
            <!-- 4 LIFE SIGNAL VECTORS SECTION -->
            <!-- ========================================== -->
            <section id="fitur" class="py-6 space-y-10">

                <div class="text-center max-w-2xl mx-auto space-y-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-nara-100 text-nara-800 text-xs font-bold tracking-wide uppercase">
                        <x-icon name="pattern" class="w-4 h-4 text-nara-600" />
                        <span>4 Life Signal Vectors</span>
                    </span>
                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-slate-900 tracking-tight">
                        Lebih dari Sekadar Pelacak Suasana Hati
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Kesejahteraan batin tidak berdiri sendiri. NARA memantau keterkaitan 4 dimensi utama secara simultan untuk melihat gambaran utuh hidupmu.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

                    <!-- 1. Mind -->
                    <div class="bg-white rounded-3xl p-6 border border-amber-200/80 shadow-card hover:shadow-card-hover transition flex flex-col justify-between group">
                        <div class="space-y-4">
                            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center transition group-hover:scale-105">
                                <x-icon name="mind" class="w-6 h-6 text-amber-600" />
                            </div>
                            <div>
                                <h3 class="font-bold text-base text-slate-900 mb-1">Vektor Pikiran (Mind)</h3>
                                <p class="text-xs text-slate-600 leading-relaxed">
                                    Mengamati tingkat stres, overthinking, kejernihan fokus belajar/kerja, dan suasana hati batiniah secara objektif.
                                </p>
                            </div>
                        </div>
                        <div class="pt-6 mt-4 border-t border-slate-100 flex items-center justify-between text-[11px] font-bold text-amber-700">
                            <span>Refleksi Kognitif</span>
                            <span class="px-2 py-0.5 rounded-md bg-amber-50">Anti Overload</span>
                        </div>
                    </div>

                    <!-- 2. Body -->
                    <div class="bg-white rounded-3xl p-6 border border-teal-200/80 shadow-card hover:shadow-card-hover transition flex flex-col justify-between group">
                        <div class="space-y-4">
                            <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 border border-teal-200 flex items-center justify-center transition group-hover:scale-105">
                                <x-icon name="body" class="w-6 h-6 text-teal-600" />
                            </div>
                            <div>
                                <h3 class="font-bold text-base text-slate-900 mb-1">Vektor Tubuh (Body)</h3>
                                <p class="text-xs text-slate-600 leading-relaxed">
                                    Melacak durasi & kualitas tidur malam, tingkat energi fisik, dan durasi gerak aktif harian yang menopang harimu.
                                </p>
                            </div>
                        </div>
                        <div class="pt-6 mt-4 border-t border-slate-100 flex items-center justify-between text-[11px] font-bold text-teal-700">
                            <span>Pondasi Fisik</span>
                            <span class="px-2 py-0.5 rounded-md bg-teal-50">Sleep Hygiene</span>
                        </div>
                    </div>

                    <!-- 3. Social -->
                    <div class="bg-white rounded-3xl p-6 border border-purple-200/80 shadow-card hover:shadow-card-hover transition flex flex-col justify-between group">
                        <div class="space-y-4">
                            <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 border border-purple-200 flex items-center justify-center transition group-hover:scale-105">
                                <x-icon name="social" class="w-6 h-6 text-purple-600" />
                            </div>
                            <div>
                                <h3 class="font-bold text-base text-slate-900 mb-1">Vektor Sosial (Social)</h3>
                                <p class="text-xs text-slate-600 leading-relaxed">
                                    Menjaga kehangatan interaksi pertemanan, mendeteksi rasa terasing/kesepian, dan memitigasi gesekan konflik.
                                </p>
                            </div>
                        </div>
                        <div class="pt-6 mt-4 border-t border-slate-100 flex items-center justify-between text-[11px] font-bold text-purple-700">
                            <span>Koneksi Hangat</span>
                            <span class="px-2 py-0.5 rounded-md bg-purple-50">Circle Support</span>
                        </div>
                    </div>

                    <!-- 4. Life -->
                    <div class="bg-white rounded-3xl p-6 border border-rose-200/80 shadow-card hover:shadow-card-hover transition flex flex-col justify-between group">
                        <div class="space-y-4">
                            <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 border border-rose-200 flex items-center justify-center transition group-hover:scale-105">
                                <x-icon name="life" class="w-6 h-6 text-rose-600" />
                            </div>
                            <div>
                                <h3 class="font-bold text-base text-slate-900 mb-1">Vektor Hidup (Life)</h3>
                                <p class="text-xs text-slate-600 leading-relaxed">
                                    Menghubungkan tekanan beban tugas kuliah/kantor, kecemasan finansial, dan progres impian pribadimu.
                                </p>
                            </div>
                        </div>
                        <div class="pt-6 mt-4 border-t border-slate-100 flex items-center justify-between text-[11px] font-bold text-rose-700">
                            <span>Konteks Nyata</span>
                            <span class="px-2 py-0.5 rounded-md bg-rose-50">Beban Seimbang</span>
                        </div>
                    </div>

                </div>

            </section>

            <!-- ========================================== -->
            <!-- 10 CORE MODULES BLUEPRINT SHOWCASE -->
            <!-- ========================================== -->
            <section id="modul" class="py-6 space-y-10">

                <div class="text-center max-w-2xl mx-auto space-y-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-teal-50 text-teal-800 text-xs font-bold tracking-wide uppercase">
                        <x-icon name="recovery" class="w-4 h-4 text-teal-600" />
                        <span>Ekosistem Lengkap</span>
                    </span>
                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-slate-900 tracking-tight">
                        Fitur Holistik untuk Menemani Langkahmu
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Dirancang khusus tanpa diagnosis kaku, dengan pendekatan empati berbasis bukti data riwayatmu sendiri.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                    <!-- Module 1 -->
                    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-card hover:shadow-card-hover transition space-y-3">
                        <div class="w-10 h-10 rounded-2xl bg-nara-100 text-nara-700 flex items-center justify-center">
                            <x-icon name="dashboard" class="w-5 h-5 text-nara-700" />
                        </div>
                        <h4 class="font-bold text-sm text-slate-800">What Changed? Engine</h4>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Mendeteksi pergeseran metrik 7 hari (&Delta; &ge; 15%) dan memberikan penjelasan empati tanpa istilah klinis yang menakutkan.
                        </p>
                    </div>

                    <!-- Module 2 -->
                    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-card hover:shadow-card-hover transition space-y-3">
                        <div class="w-10 h-10 rounded-2xl bg-purple-100 text-purple-700 flex items-center justify-center">
                            <x-icon name="pattern" class="w-5 h-5 text-purple-700" />
                        </div>
                        <h4 class="font-bold text-sm text-slate-800">Life Pattern Engine</h4>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Memetakan rantai sebab-akibat: deadline agenda &rarr; jam tidur turun &rarr; stres naik &rarr; energi drop.
                        </p>
                    </div>

                    <!-- Module 3 -->
                    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-card hover:shadow-card-hover transition space-y-3">
                        <div class="w-10 h-10 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center">
                            <x-icon name="sparkle" class="w-5 h-5 text-amber-700" />
                        </div>
                        <h4 class="font-bold text-sm text-slate-800">"What If?" Habit Simulator</h4>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Menghitung proyeksi hasil nyata dari data riwayatmu saat variabel tidur atau rehat terpenuhi secara teratur.
                        </p>
                    </div>

                    <!-- Module 4 -->
                    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-card hover:shadow-card-hover transition space-y-3">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
                            <x-icon name="check" class="w-5 h-5 text-emerald-700" />
                        </div>
                        <h4 class="font-bold text-sm text-slate-800">"One Small Thing" Micro-Action</h4>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Satu aksi mikro bergesekan rendah per hari yang secara cerdas ditargetkan ke sinyal terendahmu hari ini.
                        </p>
                    </div>

                    <!-- Module 5 -->
                    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-card hover:shadow-card-hover transition space-y-3">
                        <div class="w-10 h-10 rounded-2xl bg-teal-100 text-teal-700 flex items-center justify-center">
                            <x-icon name="recovery" class="w-5 h-5 text-teal-700" />
                        </div>
                        <h4 class="font-bold text-sm text-slate-800">Recovery Lab & Profile</h4>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Eksperimen rehat mandiri (Before vs After) untuk merangking aktivitas apa yang paling efektif mendongkrak energimu.
                        </p>
                    </div>

                    <!-- Module 6 -->
                    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-card hover:shadow-card-hover transition space-y-3">
                        <div class="w-10 h-10 rounded-2xl bg-indigo-100 text-indigo-700 flex items-center justify-center">
                            <x-icon name="pulse" class="w-5 h-5 text-indigo-700" />
                        </div>
                        <h4 class="font-bold text-sm text-slate-800">Pulse Komunitas Anonim</h4>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Statistik agregat mingguan solidaritas anak muda tanpa membocorkan identitas, catatan, atau email pribadi.
                        </p>
                    </div>

                </div>

            </section>

            <!-- ========================================== -->
            <!-- PRIVACY & PLEDGE SECTION -->
            <!-- ========================================== -->
            <section id="privasi" class="bg-gradient-to-br from-[#2A4869] to-[#1B2D40] rounded-3xl p-8 sm:p-12 text-white shadow-xl relative overflow-hidden border border-white/15">
                <div class="relative z-10 max-w-3xl space-y-4">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/10 border border-white/20 text-emerald-300 text-xs font-bold">
                        <x-icon name="privacy" class="w-4 h-4 text-emerald-300" />
                        <span>Jaminan Privasi Absolut</span>
                    </div>

                    <h3 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
                        "Data Kamu. Kendali Penuh Kamu. Ruang Amanmu."
                    </h3>

                    <p class="text-xs sm:text-sm text-white/80 leading-relaxed">
                        NARA tidak menggunakan API kecerdasan buatan pihak ketiga (No OpenAI/Anthropic). Seluruh pemrosesan sinyal dan rekomendasi rehat berjalan secara lokal & deterministik di server. Data catatan jurnalmu tidak pernah dijual, dipindai, atau dibagikan ke pihak mana pun.
                    </p>

                    <div class="pt-4 flex flex-wrap items-center gap-4">
                        <a href="{{ route('login') }}" class="px-6 py-3 rounded-full bg-white text-[#2A4869] hover:bg-[#F8F6F0] font-bold text-xs shadow-md transition">
                            Mulai Sekarang Secara Gratis &rarr;
                        </a>
                        <span class="text-xs text-white/60">Tanpa kartu kredit &bull; Ekspor data JSON/CSV kapan saja</span>
                    </div>
                </div>
            </section>

            <!-- ========================================== -->
            <!-- FOOTER -->
            <!-- ========================================== -->
            <footer class="pt-8 pb-12 border-t border-slate-200/80 text-xs text-slate-500 flex flex-col sm:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-3">
                    <div class="w-7 h-7 rounded-xl bg-[#436B92] text-white flex items-center justify-center font-bold text-xs">
                        N
                    </div>
                    <span class="font-extrabold text-slate-800 text-sm">NARA</span>
                    <span>&bull;</span>
                    <span>A Personal Well-being System That Understands Your Life.</span>
                </div>
                <div class="text-center sm:text-right">
                    <span>&copy; {{ date('Y') }} NARA. Dibuat dengan cinta untuk generasi muda Indonesia.</span>
                </div>
            </footer>

        </div>

    </body>
</html>

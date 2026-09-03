<!DOCTYPE html>
<html lang="id" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>NARA - Life Pattern Companion</title>

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
    <body class="min-h-screen bg-slate-50 text-slate-800 antialiased flex flex-col selection:bg-emerald-100 selection:text-emerald-900">
        
        <!-- Top Navigation -->
        <nav class="bg-white/80 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-30">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <a href="/" class="flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-emerald-800 to-teal-600 flex items-center justify-center text-white font-bold text-lg shadow-sm">
                        🌿
                    </div>
                    <div>
                        <span class="font-extrabold text-xl tracking-tight text-slate-900">NARA</span>
                        <span class="text-[10px] font-semibold text-emerald-800 bg-emerald-100 px-2 py-0.5 rounded-full ml-1">Life Pattern Companion</span>
                    </div>
                </a>

                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="px-5 py-2.5 rounded-2xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-md shadow-emerald-700/20 transition">
                            Buka Dashboard &rarr;
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-700 hover:text-emerald-800 transition">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="px-5 py-2.5 rounded-2xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-md shadow-emerald-700/20 transition">
                            Mulai Sekarang
                        </a>
                    @endauth
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <div class="relative overflow-hidden pt-12 pb-20">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-6">
                
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-50 border border-emerald-200/80 text-emerald-800 text-xs font-semibold shadow-xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>100% Deterministik &bull; Tanpa External AI API &bull; Menjaga Privasi Utuh</span>
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-slate-900 tracking-tight leading-tight">
                    Sistem Kesejahteraan Personal yang Memahami <span class="text-emerald-700 underline decoration-emerald-300 decoration-wavy">Pola Hidupmu</span>, Bukan Sekadar Mood.
                </h1>

                <p class="text-base sm:text-lg text-slate-600 max-w-3xl mx-auto leading-relaxed">
                    NARA menghubungkan 4 vektor sinyal hidupmu (Pikiran, Tubuh, Sosial, dan Beban Hidup) dengan agenda harian. Menemukan sebab-akibat kenapa kamu lelah, dan merekomendasikan langkah mikro nyata untuk memulihkannya.
                </p>

                <!-- Demo Account Quick Log-in Card for Easy Testing -->
                <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-4 rounded-2xl bg-emerald-700 hover:bg-emerald-800 text-white font-extrabold text-sm shadow-xl shadow-emerald-700/30 transition transform hover:-translate-y-0.5">
                        Mulai Jelajahi NARA &rarr;
                    </a>
                </div>

                <div class="bg-slate-100/80 border border-slate-200 rounded-2xl p-4 max-w-md mx-auto text-xs text-slate-600 flex items-center justify-between gap-4">
                    <div class="text-left">
                        <span class="font-bold text-slate-800 block">Akun Demo (Siap Uji 14 Hari Data):</span>
                        <span class="text-slate-500">Email: <code>nara@wellbeing.id</code> | Password: <code>password</code></span>
                    </div>
                    <a href="{{ route('login') }}" class="px-3 py-1.5 rounded-xl bg-white border border-slate-300 hover:bg-slate-50 font-bold text-emerald-700 flex-shrink-0">
                        Masuk Demo
                    </a>
                </div>

            </div>
        </div>

        <!-- 4 Vectors Feature Grid -->
        <div class="bg-white py-16 border-y border-slate-200/80">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
                
                <div class="text-center max-w-2xl mx-auto">
                    <span class="text-xs font-bold uppercase tracking-widest text-emerald-700">4 Life Signal Vectors</span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">Lebih dari Sekadar Pelacak Suasana Hati</h2>
                    <p class="text-xs sm:text-sm text-slate-500 mt-2">Kesejahteraan tidak berdiri sendiri. NARA memantau keterkaitan 4 dimensi utama secara simultan.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    
                    <!-- 1. Mind -->
                    <div class="p-6 rounded-3xl bg-amber-50/50 border border-amber-200/60 flex flex-col justify-between">
                        <div>
                            <span class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-800 flex items-center justify-center text-2xl font-bold mb-4">🧠</span>
                            <h3 class="font-bold text-base text-slate-900 mb-1">Vektor Pikiran (Mind)</h3>
                            <p class="text-xs text-slate-600 leading-relaxed mb-4">
                                Mengamati tingkat stres, overthinking, kejernihan fokus belajar/kerja, dan suasana hati batiniah.
                            </p>
                        </div>
                        <span class="text-[11px] font-bold text-amber-700">Refleksi Kognitif &bull; Anti Overload</span>
                    </div>

                    <!-- 2. Body -->
                    <div class="p-6 rounded-3xl bg-cyan-50/50 border border-cyan-200/60 flex flex-col justify-between">
                        <div>
                            <span class="w-12 h-12 rounded-2xl bg-cyan-100 text-cyan-800 flex items-center justify-center text-2xl font-bold mb-4">⚡</span>
                            <h3 class="font-bold text-base text-slate-900 mb-1">Vektor Tubuh (Body)</h3>
                            <p class="text-xs text-slate-600 leading-relaxed mb-4">
                                Melacak durasi & kualitas tidur malam, tingkat energi fisik, dan durasi gerak aktif harian.
                            </p>
                        </div>
                        <span class="text-[11px] font-bold text-cyan-700">Pondasi Fisik &bull; Sleep Hygiene</span>
                    </div>

                    <!-- 3. Social -->
                    <div class="p-6 rounded-3xl bg-purple-50/50 border border-purple-200/60 flex flex-col justify-between">
                        <div>
                            <span class="w-12 h-12 rounded-2xl bg-purple-100 text-purple-800 flex items-center justify-center text-2xl font-bold mb-4">👥</span>
                            <h3 class="font-bold text-base text-slate-900 mb-1">Vektor Sosial (Social)</h3>
                            <p class="text-xs text-slate-600 leading-relaxed mb-4">
                                Menjaga kehangatan interaksi pertemanan, mendeteksi rasa terasing/kesepian, dan gesekan konflik.
                            </p>
                        </div>
                        <span class="text-[11px] font-bold text-purple-700">Koneksi Hangat &bull; Circle Support</span>
                    </div>

                    <!-- 4. Life -->
                    <div class="p-6 rounded-3xl bg-rose-50/50 border border-rose-200/60 flex flex-col justify-between">
                        <div>
                            <span class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-800 flex items-center justify-center text-2xl font-bold mb-4">🎯</span>
                            <h3 class="font-bold text-base text-slate-900 mb-1">Vektor Hidup (Life)</h3>
                            <p class="text-xs text-slate-600 leading-relaxed mb-4">
                                Menghubungkan tekanan beban tugas kuliah/kantor, kecemasan finansial, dan progres impianmu.
                            </p>
                        </div>
                        <span class="text-[11px] font-bold text-rose-700">Konteks Nyata &bull; Keseimbangan Beban</span>
                    </div>

                </div>

            </div>
        </div>

        <!-- 10 Modules Blueprint Highlights -->
        <div class="py-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <div class="text-center max-w-2xl mx-auto">
                <span class="text-xs font-bold uppercase tracking-widest text-emerald-700">10 Core Modules</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">Ekosistem Kesejahteraan Holistik</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 text-xs">
                <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm">
                    <span class="text-lg block mb-2">🔍</span>
                    <h4 class="font-bold text-sm text-slate-800 mb-1">What Changed? Engine</h4>
                    <p class="text-slate-500 leading-relaxed">Mendeteksi pergeseran metrik 7 hari (&Delta; &ge; 15%) dan memberikan penjelasan empati tanpa diagnosis kaku.</p>
                </div>

                <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm">
                    <span class="text-lg block mb-2">🔮</span>
                    <h4 class="font-bold text-sm text-slate-800 mb-1">Life Pattern Engine</h4>
                    <p class="text-slate-500 leading-relaxed">Memetakan rantai sebab-akibat: deadline agenda &rarr; jam tidur turun &rarr; stres naik &rarr; energi drop.</p>
                </div>

                <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm">
                    <span class="text-lg block mb-2">🎯</span>
                    <h4 class="font-bold text-sm text-slate-800 mb-1">"What If?" Habit Simulator</h4>
                    <p class="text-slate-500 leading-relaxed">Menghitung proyeksi hasil nyata dari data riwayatmu saat variabel tidur atau rehat terpenuhi.</p>
                </div>

                <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm">
                    <span class="text-lg block mb-2">✨</span>
                    <h4 class="font-bold text-sm text-slate-800 mb-1">"One Small Thing" Micro-Action</h4>
                    <p class="text-slate-500 leading-relaxed">Satu aksi mikro bergesekan rendah per hari yang secara cerdas ditargetkan ke sinyal terendahmu.</p>
                </div>

                <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm">
                    <span class="text-lg block mb-2">🧪</span>
                    <h4 class="font-bold text-sm text-slate-800 mb-1">Recovery Lab & Profile</h4>
                    <p class="text-slate-500 leading-relaxed">Eksperimen rehat mandiri untuk merangking aktivitas apa yang paling efektif mendongkrak energimu.</p>
                </div>

                <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm">
                    <span class="text-lg block mb-2">🌐</span>
                    <h4 class="font-bold text-sm text-slate-800 mb-1">Pulse Komunitas Anonim</h4>
                    <p class="text-slate-500 leading-relaxed">Statistik agregat mingguan solidaritas anak muda tanpa membocorkan identitas pribadi.</p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-slate-900 text-white py-12 mt-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-6 text-xs text-slate-400">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold text-sm">🌿</div>
                    <span class="font-extrabold text-white text-sm">NARA</span>
                    <span>&bull;</span>
                    <span>A Personal Well-being System That Understands Your Life.</span>
                </div>
                <div>
                    <span>&copy; {{ date('Y') }} NARA. Dibuat dengan cinta untuk generasi muda Indonesia.</span>
                </div>
            </div>
        </footer>

    </body>
</html>

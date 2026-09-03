<nav x-data="{ open: false }" class="bg-white/90 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-30">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 group">
                        <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-emerald-800 to-teal-600 flex items-center justify-center text-white font-bold text-lg shadow-sm group-hover:scale-105 transition">
                            🌿
                        </div>
                        <div>
                            <span class="font-extrabold text-xl tracking-tight text-slate-800 group-hover:text-emerald-700 transition">NARA</span>
                            <span class="hidden sm:inline-block text-[10px] font-medium text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full ml-1 border border-emerald-200/60">Life Companion</span>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links (Desktop) -->
                <div class="hidden lg:flex lg:items-center lg:space-x-1 ml-8">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-3 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('dashboard') ? 'bg-emerald-50 text-emerald-800 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }}">
                        📊 Dashboard
                    </a>

                    <a href="{{ route('pattern.index') }}" class="inline-flex items-center px-3 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('pattern.*') ? 'bg-emerald-50 text-emerald-800 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }}">
                        🔮 Pola & What-If
                    </a>

                    <a href="{{ route('recovery.index') }}" class="inline-flex items-center px-3 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('recovery.*') ? 'bg-emerald-50 text-emerald-800 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }}">
                        🧪 Recovery Lab
                    </a>

                    <a href="{{ route('pulse.index') }}" class="inline-flex items-center px-3 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('pulse.*') ? 'bg-emerald-50 text-emerald-800 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }}">
                        🌐 Pulse Komunitas
                    </a>

                    <a href="{{ route('circle.index') }}" class="inline-flex items-center px-3 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('circle.*') ? 'bg-emerald-50 text-emerald-800 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }}">
                        🤝 Lingkaran Support
                    </a>

                    <a href="{{ route('reflection.index') }}" class="inline-flex items-center px-3 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('reflection.*') ? 'bg-emerald-50 text-emerald-800 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }}">
                        🧘 Refleksi
                    </a>

                    <a href="{{ route('chat.index') }}" class="inline-flex items-center px-3 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('chat.*') ? 'bg-emerald-50 text-emerald-800 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }}">
                        💬 Chat NARA
                    </a>

                    <a href="{{ route('privacy.index') }}" class="inline-flex items-center px-3 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('privacy.*') ? 'bg-emerald-50 text-emerald-800 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }}">
                        🔒 Privasi
                    </a>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden lg:flex lg:items-center lg:gap-3">
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-100 hover:bg-slate-200/80 border border-slate-200 text-sm font-medium text-slate-700 transition">
                        <img class="w-6 h-6 rounded-full bg-emerald-100 border border-emerald-300" src="https://api.dicebear.com/7.x/bottts-neutral/svg?seed={{ urlencode(Auth::user()->name) }}" alt="Avatar">
                        <span>{{ Auth::user()->name }}</span>
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <div x-show="open" @click.outside="open = false" x-transition class="absolute right-0 mt-2 w-48 rounded-2xl bg-white shadow-xl border border-slate-200/80 py-2 z-50 text-sm">
                        <div class="px-4 py-2 border-b border-slate-100 text-xs text-slate-400">
                            Masuk sebagai <strong class="text-slate-700 block truncate">{{ Auth::user()->email }}</strong>
                        </div>
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-slate-700 hover:bg-slate-50 transition">⚙️ Pengaturan Profil</a>
                        <a href="{{ route('privacy.index') }}" class="block px-4 py-2 text-slate-700 hover:bg-slate-50 transition">🔒 Pusat Privasi Data</a>
                        
                        <form method="POST" action="{{ route('logout') }}" class="border-t border-slate-100 mt-1">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-rose-600 hover:bg-rose-50 transition font-medium">
                                🚪 Keluar Akun
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Hamburger (Mobile) -->
            <div class="-mr-2 flex items-center lg:hidden">
                <button @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-xl text-slate-500 hover:text-slate-700 hover:bg-slate-100 focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu (Mobile) -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden lg:hidden border-b border-slate-200 bg-white px-4 pt-2 pb-4 space-y-1">
        <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-xl text-base font-medium {{ request()->routeIs('dashboard') ? 'bg-emerald-50 text-emerald-800 font-semibold' : 'text-slate-600' }}">
            📊 Dashboard
        </a>
        <a href="{{ route('pattern.index') }}" class="block px-3 py-2 rounded-xl text-base font-medium {{ request()->routeIs('pattern.*') ? 'bg-emerald-50 text-emerald-800 font-semibold' : 'text-slate-600' }}">
            🔮 Pola & What-If
        </a>
        <a href="{{ route('recovery.index') }}" class="block px-3 py-2 rounded-xl text-base font-medium {{ request()->routeIs('recovery.*') ? 'bg-emerald-50 text-emerald-800 font-semibold' : 'text-slate-600' }}">
            🧪 Recovery Lab
        </a>
        <a href="{{ route('pulse.index') }}" class="block px-3 py-2 rounded-xl text-base font-medium {{ request()->routeIs('pulse.*') ? 'bg-emerald-50 text-emerald-800 font-semibold' : 'text-slate-600' }}">
            🌐 Pulse Komunitas
        </a>
        <a href="{{ route('circle.index') }}" class="block px-3 py-2 rounded-xl text-base font-medium {{ request()->routeIs('circle.*') ? 'bg-emerald-50 text-emerald-800 font-semibold' : 'text-slate-600' }}">
            🤝 Lingkaran Support
        </a>
        <a href="{{ route('reflection.index') }}" class="block px-3 py-2 rounded-xl text-base font-medium {{ request()->routeIs('reflection.*') ? 'bg-emerald-50 text-emerald-800 font-semibold' : 'text-slate-600' }}">
            🧘 Refleksi
        </a>
        <a href="{{ route('chat.index') }}" class="block px-3 py-2 rounded-xl text-base font-medium {{ request()->routeIs('chat.*') ? 'bg-emerald-50 text-emerald-800 font-semibold' : 'text-slate-600' }}">
            💬 Chat NARA
        </a>
        <a href="{{ route('privacy.index') }}" class="block px-3 py-2 rounded-xl text-base font-medium {{ request()->routeIs('privacy.*') ? 'bg-emerald-50 text-emerald-800 font-semibold' : 'text-slate-600' }}">
            🔒 Pusat Privasi
        </a>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
            <span class="text-sm font-medium text-slate-700">{{ Auth::user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-xs font-semibold text-rose-600 hover:underline">Keluar</button>
            </form>
        </div>
    </div>
</nav>

{{--
    Navigasi ini menjadi titik akses utama ke dashboard dan modul NARA.
    Link memakai named route agar perubahan URL tidak perlu diulang di banyak view,
    sementara state Alpine mengatur menu pada layar kecil.
--}}

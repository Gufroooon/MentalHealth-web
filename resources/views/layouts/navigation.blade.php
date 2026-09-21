<nav x-data="{ open: false }" class="bg-gradient-to-r from-[#557FA8] via-[#4D769E] to-[#436B92] text-white border-b border-white/15 sticky top-0 z-30 shadow-md">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Brand Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 group">
                        <div class="w-9 h-9 rounded-xl bg-white text-[#436B92] flex items-center justify-center font-black text-base shadow-sm group-hover:scale-105 transition">
                            N
                        </div>
                        <div>
                            <span class="font-black text-lg tracking-tight text-white block leading-none">NARA</span>
                            <span class="hidden lg:inline-block text-[9px] font-bold tracking-wider text-white/80 uppercase">Life Companion</span>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links (Desktop) -->
                <div class="hidden xl:flex xl:items-center xl:space-x-1.5 ml-8">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold transition {{ request()->routeIs('dashboard') ? 'bg-white text-[#263F59] shadow-sm' : 'text-white/85 hover:text-white hover:bg-white/15' }}">
                        <x-icon name="dashboard" class="w-4 h-4 {{ request()->routeIs('dashboard') ? 'text-[#436B92]' : 'text-white/70' }}" />
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('pattern.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold transition {{ request()->routeIs('pattern.*') ? 'bg-white text-[#263F59] shadow-sm' : 'text-white/85 hover:text-white hover:bg-white/15' }}">
                        <x-icon name="pattern" class="w-4 h-4 {{ request()->routeIs('pattern.*') ? 'text-[#436B92]' : 'text-white/70' }}" />
                        <span>Pola & What-If</span>
                    </a>

                    <a href="{{ route('recovery.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold transition {{ request()->routeIs('recovery.*') ? 'bg-white text-[#263F59] shadow-sm' : 'text-white/85 hover:text-white hover:bg-white/15' }}">
                        <x-icon name="recovery" class="w-4 h-4 {{ request()->routeIs('recovery.*') ? 'text-[#436B92]' : 'text-white/70' }}" />
                        <span>Recovery Lab</span>
                    </a>

                    <a href="{{ route('pulse.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold transition {{ request()->routeIs('pulse.*') ? 'bg-white text-[#263F59] shadow-sm' : 'text-white/85 hover:text-white hover:bg-white/15' }}">
                        <x-icon name="pulse" class="w-4 h-4 {{ request()->routeIs('pulse.*') ? 'text-[#436B92]' : 'text-white/70' }}" />
                        <span>Pulse Komunitas</span>
                    </a>

                    <a href="{{ route('circle.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold transition {{ request()->routeIs('circle.*') ? 'bg-white text-[#263F59] shadow-sm' : 'text-white/85 hover:text-white hover:bg-white/15' }}">
                        <x-icon name="circle" class="w-4 h-4 {{ request()->routeIs('circle.*') ? 'text-[#436B92]' : 'text-white/70' }}" />
                        <span>Lingkaran Support</span>
                    </a>

                    <a href="{{ route('reflection.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold transition {{ request()->routeIs('reflection.*') ? 'bg-white text-[#263F59] shadow-sm' : 'text-white/85 hover:text-white hover:bg-white/15' }}">
                        <x-icon name="reflection" class="w-4 h-4 {{ request()->routeIs('reflection.*') ? 'text-[#436B92]' : 'text-white/70' }}" />
                        <span>Refleksi</span>
                    </a>

                    <a href="{{ route('chat.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold transition {{ request()->routeIs('chat.*') ? 'bg-white text-[#263F59] shadow-sm' : 'text-white/85 hover:text-white hover:bg-white/15' }}">
                        <x-icon name="chat" class="w-4 h-4 {{ request()->routeIs('chat.*') ? 'text-[#436B92]' : 'text-white/70' }}" />
                        <span>Chat NARA</span>
                    </a>

                    <a href="{{ route('privacy.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold transition {{ request()->routeIs('privacy.*') ? 'bg-white text-[#263F59] shadow-sm' : 'text-white/85 hover:text-white hover:bg-white/15' }}">
                        <x-icon name="privacy" class="w-4 h-4 {{ request()->routeIs('privacy.*') ? 'text-[#436B92]' : 'text-white/70' }}" />
                        <span>Privasi</span>
                    </a>
                </div>
            </div>

            <!-- Settings Dropdown (Desktop) -->
            <div class="hidden xl:flex xl:items-center xl:gap-3">
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/15 hover:bg-white/25 border border-white/25 text-xs font-bold text-white transition">
                        <img class="w-6 h-6 rounded-full bg-white text-[#436B92] object-cover" src="https://api.dicebear.com/7.x/bottts-neutral/svg?seed={{ urlencode(Auth::user()->name) }}" alt="Avatar">
                        <span>{{ explode(' ', Auth::user()->name)[0] }}</span>
                        <svg class="w-3.5 h-3.5 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <div x-show="open" @click.outside="open = false" x-transition class="absolute right-0 mt-2 w-52 rounded-2xl bg-white text-slate-800 shadow-xl border border-slate-200/80 py-2 z-50 text-xs">
                        <div class="px-4 py-2 border-b border-slate-100 text-slate-500">
                            Masuk sebagai <strong class="text-slate-800 block truncate">{{ Auth::user()->email }}</strong>
                        </div>
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2.5 text-slate-700 hover:bg-nara-50 hover:text-nara-800 transition font-medium">
                            <x-icon name="user" class="w-4 h-4 text-slate-400" />
                            <span>Pengaturan Profil</span>
                        </a>
                        <a href="{{ route('privacy.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-slate-700 hover:bg-nara-50 hover:text-nara-800 transition font-medium">
                            <x-icon name="privacy" class="w-4 h-4 text-slate-400" />
                            <span>Pusat Privasi Data</span>
                        </a>

                        <form method="POST" action="{{ route('logout') }}" class="border-t border-slate-100 mt-1">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-2 text-left px-4 py-2.5 text-rose-600 hover:bg-rose-50 transition font-bold">
                                <x-icon name="trash" class="w-4 h-4 text-rose-500" />
                                <span>Keluar Akun</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Hamburger (Mobile / Tablet) -->
            <div class="flex items-center xl:hidden">
                <button @click="open = !open" aria-label="Buka navigasi" class="inline-flex items-center justify-center p-2 rounded-xl text-white/90 hover:text-white hover:bg-white/15 transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu (Mobile) -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden xl:hidden border-b border-white/20 bg-[#345577] px-4 pt-3 pb-5 space-y-1.5 shadow-xl">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-bold {{ request()->routeIs('dashboard') ? 'bg-white text-[#263F59]' : 'text-white/90 hover:bg-white/10' }}">
            <x-icon name="dashboard" class="w-4 h-4" />
            <span>Dashboard</span>
        </a>
        <a href="{{ route('pattern.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-bold {{ request()->routeIs('pattern.*') ? 'bg-white text-[#263F59]' : 'text-white/90 hover:bg-white/10' }}">
            <x-icon name="pattern" class="w-4 h-4" />
            <span>Pola & What-If</span>
        </a>
        <a href="{{ route('recovery.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-bold {{ request()->routeIs('recovery.*') ? 'bg-white text-[#263F59]' : 'text-white/90 hover:bg-white/10' }}">
            <x-icon name="recovery" class="w-4 h-4" />
            <span>Recovery Lab</span>
        </a>
        <a href="{{ route('pulse.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-bold {{ request()->routeIs('pulse.*') ? 'bg-white text-[#263F59]' : 'text-white/90 hover:bg-white/10' }}">
            <x-icon name="pulse" class="w-4 h-4" />
            <span>Pulse Komunitas</span>
        </a>
        <a href="{{ route('circle.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-bold {{ request()->routeIs('circle.*') ? 'bg-white text-[#263F59]' : 'text-white/90 hover:bg-white/10' }}">
            <x-icon name="circle" class="w-4 h-4" />
            <span>Lingkaran Support</span>
        </a>
        <a href="{{ route('reflection.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-bold {{ request()->routeIs('reflection.*') ? 'bg-white text-[#263F59]' : 'text-white/90 hover:bg-white/10' }}">
            <x-icon name="reflection" class="w-4 h-4" />
            <span>Refleksi</span>
        </a>
        <a href="{{ route('chat.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-bold {{ request()->routeIs('chat.*') ? 'bg-white text-[#263F59]' : 'text-white/90 hover:bg-white/10' }}">
            <x-icon name="chat" class="w-4 h-4" />
            <span>Chat NARA</span>
        </a>
        <a href="{{ route('privacy.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-bold {{ request()->routeIs('privacy.*') ? 'bg-white text-[#263F59]' : 'text-white/90 hover:bg-white/10' }}">
            <x-icon name="privacy" class="w-4 h-4" />
            <span>Pusat Privasi</span>
        </a>

        <div class="pt-4 mt-2 border-t border-white/15 flex items-center justify-between px-2 text-xs">
            <span class="font-bold text-white">{{ Auth::user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="font-bold text-rose-300 hover:text-rose-100">Keluar</button>
            </form>
        </div>
    </div>
</nav>

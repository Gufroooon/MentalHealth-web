<x-guest-layout>
    <div class="mb-6 space-y-1">
        <h2 class="text-xl sm:text-2xl font-black text-slate-800 tracking-tight">Selamat Datang Kembali</h2>
        <p class="text-xs sm:text-sm text-slate-500">Masuk untuk melihat sinyal dan pola hidupmu hari ini.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4" x-data="{
        fillDemo() {
            document.getElementById('email').value = 'nara@wellbeing.id';
            document.getElementById('password').value = 'password';
        }
    }">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-bold text-slate-700 mb-1.5">Alamat Email</label>
            <input id="email" class="w-full rounded-2xl border-slate-200 text-xs sm:text-sm focus:border-nara-500 focus:ring-nara-500 p-3" type="email" name="email" value="{{ old('email', 'nara@wellbeing.id') }}" required autofocus autocomplete="username" placeholder="nama@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="block text-xs font-bold text-slate-700">Password</label>
                @if (Route::has('password.request'))
                    <a class="text-xs text-nara-700 hover:underline font-bold" href="{{ route('password.request') }}">
                        Lupa password?
                    </a>
                @endif
            </div>
            <input id="password" class="w-full rounded-2xl border-slate-200 text-xs sm:text-sm focus:border-nara-500 focus:ring-nara-500 p-3" type="password" name="password" value="password" required autocomplete="current-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <!-- Remember Me & Demo Helper -->
        <div class="flex items-center justify-between pt-1">
            <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded-md border-slate-300 text-nara-600 focus:ring-nara-500" name="remember" checked>
                <span class="text-xs text-slate-600 font-medium">Ingat saya</span>
            </label>

            <button type="button" @click="fillDemo()" class="text-xs font-bold text-nara-800 bg-nara-50 hover:bg-nara-100 px-3 py-1 rounded-xl transition border border-nara-200/80 flex items-center gap-1">
                <x-icon name="sparkle" class="w-3.5 h-3.5 text-amber-600" />
                <span>Isi Akun Demo</span>
            </button>
        </div>

        <div class="pt-3 space-y-3">
            <button type="submit" class="w-full py-3.5 rounded-2xl bg-nara-600 hover:bg-nara-700 text-white font-bold text-xs sm:text-sm shadow-lg shadow-nara-600/20 transition transform active:scale-98">
                Masuk ke NARA &rarr;
            </button>

            <div class="text-center text-xs text-slate-500 pt-1">
                Belum punya akun?
                <a href="{{ route('register') }}" class="font-bold text-nara-700 hover:underline">Daftar sekarang</a>
            </div>
        </div>
    </form>
</x-guest-layout>

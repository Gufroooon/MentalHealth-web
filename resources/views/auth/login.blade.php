<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-xl font-bold text-slate-800">Selamat Datang Kembali</h2>
        <p class="text-xs text-slate-500 mt-0.5">Masuk untuk melihat sinyal dan pola hidupmu hari ini.</p>
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
            <label for="email" class="block text-xs font-bold text-slate-700 mb-1">Email</label>
            <input id="email" class="w-full rounded-2xl border-slate-200 text-xs focus:border-emerald-500 focus:ring-emerald-500" type="email" name="email" value="{{ old('email', 'nara@wellbeing.id') }}" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between mb-1">
                <label for="password" class="block text-xs font-bold text-slate-700">Password</label>
                @if (Route::has('password.request'))
                    <a class="text-[11px] text-emerald-700 hover:underline font-semibold" href="{{ route('password.request') }}">
                        Lupa password?
                    </a>
                @endif
            </div>
            <input id="password" class="w-full rounded-2xl border-slate-200 text-xs focus:border-emerald-500 focus:ring-emerald-500" type="password" name="password" value="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between pt-1">
            <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" name="remember" checked>
                <span class="text-xs text-slate-600">Ingat saya di perangkat ini</span>
            </label>

            <button type="button" @click="fillDemo()" class="text-[11px] font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 px-2.5 py-1 rounded-lg transition border border-emerald-200">
                ⚡ Isi Akun Demo
            </button>
        </div>

        <div class="pt-2 space-y-3">
            <button type="submit" class="w-full py-3 rounded-2xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-lg shadow-emerald-700/20 transition">
                Masuk ke NARA
            </button>

            <div class="text-center text-xs text-slate-500">
                Belum punya akun?
                <a href="{{ route('register') }}" class="font-bold text-emerald-700 hover:underline">Daftar sekarang</a>
            </div>
        </div>
    </form>
</x-guest-layout>

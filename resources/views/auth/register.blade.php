<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-xl font-bold text-slate-800">Mulai Perjalananmu</h2>
        <p class="text-xs text-slate-500 mt-0.5">Daftar akun baru NARA untuk memetakan sinyal dan pola hidupmu.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap</label>
            <input id="name" class="w-full rounded-2xl border-slate-200 text-xs focus:border-emerald-500 focus:ring-emerald-500" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Nama panggilanmu..." />
            <x-input-error :messages="$errors->get('name')" class="mt-1" />
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-bold text-slate-700 mb-1">Alamat Email</label>
            <input id="email" class="w-full rounded-2xl border-slate-200 text-xs focus:border-emerald-500 focus:ring-emerald-500" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="nama@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-xs font-bold text-slate-700 mb-1">Password</label>
            <input id="password" class="w-full rounded-2xl border-slate-200 text-xs focus:border-emerald-500 focus:ring-emerald-500"
                            type="password"
                            name="password"
                            required autocomplete="new-password"
                            placeholder="Minimal 8 karakter" />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-xs font-bold text-slate-700 mb-1">Konfirmasi Password</label>
            <input id="password_confirmation" class="w-full rounded-2xl border-slate-200 text-xs focus:border-emerald-500 focus:ring-emerald-500"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password"
                            placeholder="Ulangi password di atas" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
        </div>

        <div class="pt-2 space-y-3">
            <button type="submit" class="w-full py-3 rounded-2xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-lg shadow-emerald-700/20 transition">
                Daftar Akun NARA
            </button>

            <div class="text-center text-xs text-slate-500">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="font-bold text-emerald-700 hover:underline">Masuk di sini</a>
            </div>
        </div>
    </form>
</x-guest-layout>

<x-guest-layout>
    <div class="mb-6 space-y-1">
        <h2 class="text-xl sm:text-2xl font-black text-slate-800 tracking-tight">Mulai Perjalananmu</h2>
        <p class="text-xs sm:text-sm text-slate-500">Daftar akun baru NARA untuk memetakan sinyal dan pola hidupmu.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block text-xs font-bold text-slate-700 mb-1.5">Nama Lengkap</label>
            <input id="name" class="w-full rounded-2xl border-slate-200 text-xs sm:text-sm focus:border-nara-500 focus:ring-nara-500 p-3" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Nama panggilanmu..." />
            <x-input-error :messages="$errors->get('name')" class="mt-1" />
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-bold text-slate-700 mb-1.5">Alamat Email</label>
            <input id="email" class="w-full rounded-2xl border-slate-200 text-xs sm:text-sm focus:border-nara-500 focus:ring-nara-500 p-3" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="nama@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-xs font-bold text-slate-700 mb-1.5">Password</label>
            <input id="password" class="w-full rounded-2xl border-slate-200 text-xs sm:text-sm focus:border-nara-500 focus:ring-nara-500 p-3"
                            type="password"
                            name="password"
                            required autocomplete="new-password"
                            placeholder="Minimal 8 karakter" />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-xs font-bold text-slate-700 mb-1.5">Konfirmasi Password</label>
            <input id="password_confirmation" class="w-full rounded-2xl border-slate-200 text-xs sm:text-sm focus:border-nara-500 focus:ring-nara-500 p-3"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password"
                            placeholder="Ulangi password di atas" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
        </div>

        <div class="pt-3 space-y-3">
            <button type="submit" class="w-full py-3.5 rounded-2xl bg-nara-600 hover:bg-nara-700 text-white font-bold text-xs sm:text-sm shadow-lg shadow-nara-600/20 transition transform active:scale-98">
                Daftar Akun NARA &rarr;
            </button>

            <div class="text-center text-xs text-slate-500 pt-1">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="font-bold text-nara-700 hover:underline">Masuk di sini</a>
            </div>
        </div>
    </form>
</x-guest-layout>

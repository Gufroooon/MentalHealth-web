<x-guest-layout>
    <div class="mb-6 space-y-1">
        <h2 class="text-xl sm:text-2xl font-black text-slate-800 tracking-tight">Lupa Password?</h2>
        <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
            Tidak masalah. Masukkan alamat email yang terdaftar dan kami akan mengirimkan tautan untuk mengatur ulang passwordmu.
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-bold text-slate-700 mb-1.5">Alamat Email</label>
            <input id="email" class="w-full rounded-2xl border-slate-200 text-xs sm:text-sm focus:border-nara-500 focus:ring-nara-500 p-3" type="email" name="email" :value="old('email')" required autofocus placeholder="nama@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <div class="pt-3 space-y-3">
            <button type="submit" class="w-full py-3.5 rounded-2xl bg-nara-600 hover:bg-nara-700 text-white font-bold text-xs sm:text-sm shadow-lg shadow-nara-600/20 transition">
                Kirim Tautan Reset Password
            </button>

            <div class="text-center text-xs text-slate-500 pt-1">
                <a href="{{ route('login') }}" class="font-bold text-nara-700 hover:underline">&larr; Kembali ke halaman Masuk</a>
            </div>
        </div>
    </form>
</x-guest-layout>

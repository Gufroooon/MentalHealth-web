<x-app-layout>
    <x-slot name="header">
        <div>
            <span class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-slate-800 bg-slate-200 px-3 py-1 rounded-full border border-slate-300/60">
                <x-icon name="user" class="w-3.5 h-3.5 text-slate-700" />
                <span>Pengaturan Akun</span>
            </span>
            <h2 class="font-black text-2xl sm:text-3xl text-slate-800 tracking-tight mt-1.5">
                Pengaturan Profil Pengguna
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                Kelola informasi akun, kata sandi, dan data personalmu di NARA.
            </p>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <div class="p-6 sm:p-8 bg-white border border-slate-200/80 shadow-card rounded-3xl">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="p-6 sm:p-8 bg-white border border-slate-200/80 shadow-card rounded-3xl">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="p-6 sm:p-8 bg-rose-50/70 border border-rose-200/80 shadow-card rounded-3xl">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>

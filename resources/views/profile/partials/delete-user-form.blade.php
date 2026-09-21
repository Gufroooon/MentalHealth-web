<section class="space-y-4">
    <header class="space-y-1">
        <h2 class="text-lg font-black text-rose-900">
            Hapus Akun Pengguna
        </h2>

        <p class="text-xs text-rose-700">
            Setelah akunmu dihapus, seluruh data riwayat sinyal, sesi pemulihan, dan kontak lingkaran support akan dihapus secara permanen dari server.
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >Hapus Akun</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 sm:p-7 space-y-4">
            @csrf
            @method('delete')

            <h2 class="text-lg font-black text-slate-900">
                Apakah kamu yakin ingin menghapus akunmu?
            </h2>

            <p class="text-xs text-slate-600 leading-relaxed">
                Tindakan ini tidak dapat dibatalkan. Masukkan kata sandimu untuk mengonfirmasi bahwa kamu ingin menghapus akun secara permanen.
            </p>

            <div class="mt-4">
                <x-input-label for="password" value="Kata Sandi" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-full sm:w-3/4"
                    placeholder="Masukkan kata sandi untuk konfirmasi"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end gap-2.5 pt-2 border-t border-slate-100">
                <x-secondary-button x-on:click="$dispatch('close')">
                    Batal
                </x-secondary-button>

                <x-danger-button>
                    Ya, Hapus Akun Saya
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>

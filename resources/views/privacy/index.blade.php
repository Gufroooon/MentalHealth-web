{{--
    Dokumentasi file: View Blade.

    Menjelaskan tanggung jawab file resources/views/privacy/index.blade.php serta hubungan data atau UI-nya dengan bagian aplikasi lain.
--}}
<x-app-layout>
    <x-slot name="header">
        <div>
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-800 bg-slate-200 px-3 py-1 rounded-full">
                🔒 Kendali Data & Privasi
            </span>
            <h2 class="font-extrabold text-2xl text-slate-800 tracking-tight mt-1">
                Pusat Transparansi & Kontrol Privasi
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                "Data Kamu. Kendali Penuh Kamu. Apa yang kamu catat tetap menjadi milikmu."
            </p>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8" x-data="{ openWipeModal: false }">

        <!-- Onboarding & Trust Banner -->
        <div class="bg-gradient-to-r from-slate-900 to-slate-800 rounded-3xl p-6 text-white shadow-xl flex flex-col md:flex-row items-start md:items-center justify-between gap-6 border border-slate-700/60">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 border border-emerald-400/30 flex items-center justify-center text-emerald-400 text-2xl flex-shrink-0">
                    🛡️
                </div>
                <div>
                    <h3 class="text-lg font-bold text-white mb-1">Prinsip Privasi NARA Tanpa Kompromi</h3>
                    <p class="text-xs text-slate-300 leading-relaxed max-w-2xl">
                        NARA tidak menggunakan API kecerdasan buatan pihak ketiga (No OpenAI/Anthropic). Seluruh pemrosesan sinyal dan rekomendasi rehat berjalan secara <strong>lokal & deterministik</strong> di server. Kami tidak menjual data pengguna untuk iklan atau pelacakan apa pun.
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('privacy.export.json') }}" class="px-4 py-2.5 rounded-2xl bg-slate-700 hover:bg-slate-600 text-white font-bold text-xs transition flex items-center gap-1.5">
                    <span>📥 Unduh JSON</span>
                </a>
                <a href="{{ route('privacy.export.csv') }}" class="px-4 py-2.5 rounded-2xl bg-emerald-700 hover:bg-emerald-600 text-white font-bold text-xs transition flex items-center gap-1.5">
                    <span>📊 Unduh CSV</span>
                </a>
            </div>
        </div>

        <!-- Privacy Controls Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Pulse Participation Toggle -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 rounded-2xl bg-indigo-100 text-indigo-800 flex items-center justify-center font-bold mb-3">
                        🌐
                    </div>
                    <h4 class="font-bold text-base text-slate-800 mb-1">Partisipasi Pulse Komunitas</h4>
                    <p class="text-xs text-slate-500 leading-relaxed mb-4">
                        Mengizinkan agregasi skor sinyalmu secara anonim ke dalam tren mingguan solidaritas anak muda (tanpa nama/email/catatan).
                    </p>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs font-semibold {{ $profile->participate_pulse ? 'text-emerald-700' : 'text-slate-400' }}">
                        {{ $profile->participate_pulse ? '✓ Aktif (Anonim)' : '✕ Dinonaktifkan' }}
                    </span>
                    <form action="{{ route('privacy.toggle-pulse') }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold transition {{ $profile->participate_pulse ? 'bg-slate-100 hover:bg-slate-200 text-slate-700' : 'bg-emerald-700 text-white' }}">
                            {{ $profile->participate_pulse ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Export Data Portal -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold mb-3">
                        📦
                    </div>
                    <h4 class="font-bold text-base text-slate-800 mb-1">Ekspor Data Portabel</h4>
                    <p class="text-xs text-slate-500 leading-relaxed mb-4">
                        Unduh seluruh catatan sinyal hidup, profil pemulihan, riwayat lingkaran support, dan refleksi jurnalmu kapan saja.
                    </p>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center gap-2">
                    <a href="{{ route('privacy.export.json') }}" class="flex-1 text-center py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                        JSON Lengkap
                    </a>
                    <a href="{{ route('privacy.export.csv') }}" class="flex-1 text-center py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold text-xs transition">
                        Spreadsheet CSV
                    </a>
                </div>
            </div>

            <!-- Full Account & Data Wipe (Danger Zone) -->
            <div class="bg-rose-50/50 rounded-3xl p-6 border border-rose-200/70 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 rounded-2xl bg-rose-100 text-rose-800 flex items-center justify-center font-bold mb-3">
                        🗑️
                    </div>
                    <h4 class="font-bold text-base text-rose-900 mb-1">Hapus Seluruh Data & Akun</h4>
                    <p class="text-xs text-rose-700/80 leading-relaxed mb-4">
                        Menghapus akun, check-in, catatan jurnal, dan seluruh data pribadimu secara permanen dari server tanpa meninggalkan salinan cadangan.
                    </p>
                </div>

                <div class="pt-4 border-t border-rose-200">
                    <button @click="openWipeModal = true" class="w-full py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-md shadow-rose-600/20 transition">
                        Hapus Permanen Akun & Data
                    </button>
                </div>
            </div>

        </div>

        <!-- Individual Check-ins Deletion Table -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-800">Riwayat Check-in Tersimpan ({{ $checkins->count() }} Hari)</h3>
                    <p class="text-xs text-slate-500">Kamu dapat menghapus entri hari tertentu yang tidak ingin kamu simpan.</p>
                </div>
            </div>

            <div class="overflow-x-auto max-h-96">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-slate-400 uppercase font-bold text-[10px] sticky top-0">
                        <tr>
                            <th class="p-3">Tanggal</th>
                            <th class="p-3">Skor Holistik</th>
                            <th class="p-3">Mind</th>
                            <th class="p-3">Body</th>
                            <th class="p-3">Social</th>
                            <th class="p-3">Life</th>
                            <th class="p-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($checkins as $c)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="p-3 font-semibold text-slate-800">{{ $c->date->format('d M Y') }}</td>
                                <td class="p-3 font-extrabold text-emerald-700">{{ $c->overall_wellbeing_score }} pt</td>
                                <td class="p-3">{{ $c->mind_score }}</td>
                                <td class="p-3">{{ $c->body_score }}</td>
                                <td class="p-3">{{ $c->social_score }}</td>
                                <td class="p-3">{{ $c->life_score }}</td>
                                <td class="p-3 text-right">
                                    <form action="{{ route('privacy.checkin.delete', $c->id) }}" method="POST" onsubmit="return confirm('Hapus permanen check-in tanggal {{ $c->date->format('d M Y') }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 hover:underline font-semibold text-xs">
                                            Hapus Entri
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-4 text-center text-slate-400">Belum ada riwayat check-in.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Privacy Logs Audit Trail -->
        @if ($privacyLogs->isNotEmpty())
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm">
                <h3 class="text-base font-bold text-slate-800 mb-3">Audit Jejak Aksi Privasi (Privacy Logs)</h3>
                <div class="divide-y divide-slate-100 text-xs">
                    @foreach ($privacyLogs as $log)
                        <div class="py-2.5 flex items-center justify-between gap-4 text-slate-500">
                            <div>
                                <span class="font-bold text-slate-800 capitalize">{{ str_replace('_', ' ', $log->action_type) }}</span>
                                <span class="text-[10px] text-slate-400 block">IP: {{ $log->ip_address ?? '127.0.0.1' }}</span>
                            </div>
                            <span class="text-[11px] text-slate-400">{{ $log->created_at->translatedFormat('d M Y, H:i') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>

    <!-- Wipe Confirmation Modal -->
    <div x-show="openWipeModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm" @click="openWipeModal = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white rounded-3xl shadow-2xl p-6 w-full max-w-md border border-slate-200 text-center">
                <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center text-2xl mx-auto mb-4">
                    ⚠️
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-2">Konfirmasi Hapus Akun & Seluruh Data</h3>
                <p class="text-xs text-slate-500 leading-relaxed mb-6">
                    Tindakan ini bersifat <strong>permanen dan tidak dapat dibatalkan</strong>. Semua grafik sinyal, riwayat pemulihan, catatan jurnal refleksi, dan kontak lingkaran support akan dihapus seketika dari basis data.
                </p>

                <div class="flex items-center justify-center gap-3">
                    <button type="button" @click="openWipeModal = false" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                        Batal
                    </button>
                    <form action="{{ route('privacy.wipe') }}" method="POST">
                        @csrf
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-lg shadow-rose-600/30 transition">
                            Ya, Hapus Semuanya
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

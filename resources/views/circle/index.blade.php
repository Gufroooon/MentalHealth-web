{{--
    Dokumentasi file: View Blade.

    Menjelaskan tanggung jawab file resources/views/circle/index.blade.php serta hubungan data atau UI-nya dengan bagian aplikasi lain.
--}}
<x-app-layout>
    <x-slot name="header">
        <div>
            <span class="text-xs font-semibold uppercase tracking-wider text-rose-800 bg-rose-100 px-3 py-1 rounded-full">
                🤝 Jaringan Pendukung
            </span>
            <h2 class="font-extrabold text-2xl text-slate-800 tracking-tight mt-1">
                Circle: Lingkaran Aman & Support Terpercaya
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                Kirim sinyal support ke orang-orang terdekat saat harimu terasa berat tanpa harus menjelaskan panjang lebar.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <button @click="$dispatch('open-add-member')" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-sm shadow-sm transition">
                <span>+ Tambah Kontak</span>
            </button>
            <button @click="$dispatch('open-ping-modal')" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-sm shadow-md shadow-rose-600/20 transition transform active:scale-95">
                <span>💌 "Hari ini lagi agak berat"</span>
            </button>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        <!-- Privacy Shield Banner -->
        <div class="bg-gradient-to-r from-rose-950 to-slate-900 rounded-3xl p-6 text-white shadow-xl flex flex-col md:flex-row items-start md:items-center justify-between gap-6 border border-rose-900/40">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-rose-500/20 border border-rose-400/30 flex items-center justify-center text-rose-300 text-2xl flex-shrink-0">
                    🛡️
                </div>
                <div>
                    <h3 class="text-lg font-bold text-white mb-1">Prinsip Privasi Absolut Support Circle</h3>
                    <p class="text-xs text-rose-100/90 leading-relaxed max-w-2xl">
                        NARA <strong>TIDAK PERNAH</strong> mengirimkan skor sinyal, grafik angka, atau isi catatan jurnal pribadimu ke siapa pun. Saat kamu menekan tombol support, kontak terpercayamu hanya menerima sinyal empati bersih: <em>"Seseorang di lingkaranmu butuh sedikit dukungan hari ini."</em>
                    </p>
                </div>
            </div>

            <button @click="$dispatch('open-ping-modal')" class="px-5 py-3 rounded-2xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs shadow-lg shadow-rose-600/30 transition flex-shrink-0">
                Kirim Sinyal Dukungan
            </button>
        </div>

        <!-- Members Grid -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-slate-800">Kontak Terpercaya dalam Lingkaranmu ({{ $members->count() }})</h3>
                    <p class="text-xs text-slate-500">Orang-orang pilihan yang siap hadir mendengarkan tanpa menghakimi.</p>
                </div>
                <button @click="$dispatch('open-add-member')" class="text-xs font-bold text-rose-600 hover:underline">
                    + Tambah Orang Terpercaya
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse ($members as $member)
                    <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                        <div>
                            <div class="flex items-start justify-between gap-2 mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-2xl bg-rose-100 text-rose-700 flex items-center justify-center font-bold text-base">
                                        {{ strtoupper(substr($member->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-slate-800">{{ $member->name }}</h4>
                                        <span class="text-[10px] font-semibold uppercase tracking-wider text-rose-700 bg-rose-50 px-2 py-0.5 rounded-full">
                                            {{ ucfirst($member->relationship_type) }}
                                        </span>
                                    </div>
                                </div>

                                <form action="{{ route('circle.members.destroy', $member->id) }}" method="POST" onsubmit="return confirm('Hapus kontak ini dari lingkaranmu?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-slate-300 hover:text-rose-600 text-xs p-1">✕</button>
                                </form>
                            </div>

                            <div class="space-y-1 text-xs text-slate-500 mb-4">
                                @if ($member->phone)
                                    <div class="flex items-center gap-1.5">
                                        <span>📱</span>
                                        <span>{{ $member->phone }}</span>
                                    </div>
                                @endif
                                @if ($member->email)
                                    <div class="flex items-center gap-1.5 truncate">
                                        <span>✉️</span>
                                        <span class="truncate">{{ $member->email }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                            <span>Status: <strong class="text-emerald-600">Aktif Terhubung</strong></span>
                            @if ($member->last_pinged_at)
                                <span>Terakhir disapa: {{ $member->last_pinged_at->diffForHumans() }}</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="sm:col-span-2 lg:col-span-3 bg-white rounded-3xl p-8 border border-slate-200 text-center">
                        <span class="text-3xl block mb-2">🤝</span>
                        <h4 class="font-bold text-slate-800 mb-1">Lingkaran Support Masih Kosong</h4>
                        <p class="text-xs text-slate-500 mb-4">Tambahkan 1–3 orang terdekat (sahabat, keluarga, pasangan, mentor) yang kamu percaya.</p>
                        <button @click="$dispatch('open-add-member')" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition">
                            Tambah Kontak Pertama
                        </button>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Support Pings History -->
        @if ($pingsHistory->isNotEmpty())
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm">
                <h3 class="text-base font-bold text-slate-800 mb-4">Riwayat Sinyal Support yang Pernah Dikirim</h3>
                
                <div class="divide-y divide-slate-100">
                    @foreach ($pingsHistory as $ping)
                        <div class="py-3.5 flex items-center justify-between gap-4 text-xs">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold">💌</span>
                                <div>
                                    <span class="font-bold text-slate-800">
                                        @if ($ping->support_type === 'vent') Sinyal: Butuh Curhat Singkat
                                        @elseif ($ping->support_type === 'hangout') Sinyal: Ajak Keluar / Rehat Santai
                                        @elseif ($ping->support_type === 'quiet_presence') Sinyal: Cuma Butuh Ditemani
                                        @else Sinyal: Sedang Butuh Dukungan
                                        @endif
                                    </span>
                                    <p class="text-[11px] text-slate-400">{{ $ping->created_at->translatedFormat('d F Y, H:i') }} &bull; Terkirim ke {{ $ping->recipients_count }} kontak</p>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 font-semibold text-[11px]">Terkirim Aman</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>

    <!-- Modal Kirim Sinyal Dukungan -->
    <div x-data="{
        open: false,
        supportType: 'general',
        customNote: ''
    }" @open-ping-modal.window="open = true" x-show="open" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="open = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white rounded-3xl shadow-2xl p-6 w-full max-w-lg border border-slate-200">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <span class="text-xs font-bold uppercase text-rose-600">Privacy-First Support Ping</span>
                        <h3 class="text-lg font-bold text-slate-800">Kirim Sinyal Support Hari Ini</h3>
                    </div>
                    <button @click="open = false" class="text-slate-400 hover:text-slate-600 text-lg">✕</button>
                </div>

                <p class="text-xs text-slate-500 mb-4 leading-relaxed">
                    Pilih jenis kehadiran yang paling kamu butuhkan saat ini dari kontak di lingkaran amanmu.
                </p>

                <form action="{{ route('circle.ping') }}" method="POST" class="space-y-4 text-xs">
                    @csrf
                    
                    <div class="space-y-2">
                        <label class="font-bold text-slate-700 block">Jenis Kehadiran yang Diinginkan:</label>
                        
                        <label class="p-3 rounded-2xl border flex items-center gap-3 cursor-pointer transition" :class="supportType === 'general' ? 'border-rose-500 bg-rose-50/50' : 'border-slate-200 hover:bg-slate-50'">
                            <input type="radio" name="support_type" value="general" x-model="supportType" class="text-rose-600 focus:ring-rose-500">
                            <div>
                                <strong class="text-slate-800 block text-xs">Sinyal Umum ("Hari ini agak berat")</strong>
                                <span class="text-[11px] text-slate-500">Cuma mau ngasih tahu kalau energimu lagi low.</span>
                            </div>
                        </label>

                        <label class="p-3 rounded-2xl border flex items-center gap-3 cursor-pointer transition" :class="supportType === 'vent' ? 'border-rose-500 bg-rose-50/50' : 'border-slate-200 hover:bg-slate-50'">
                            <input type="radio" name="support_type" value="vent" x-model="supportType" class="text-rose-600 focus:ring-rose-500">
                            <div>
                                <strong class="text-slate-800 block text-xs">Butuh Tempat Curhat Singkat</strong>
                                <span class="text-[11px] text-slate-500">Pengin cerita sedikit tanpa butuh dihakimi atau dinasihati keras.</span>
                            </div>
                        </label>

                        <label class="p-3 rounded-2xl border flex items-center gap-3 cursor-pointer transition" :class="supportType === 'hangout' ? 'border-rose-500 bg-rose-50/50' : 'border-slate-200 hover:bg-slate-50'">
                            <input type="radio" name="support_type" value="hangout" x-model="supportType" class="text-rose-600 focus:ring-rose-500">
                            <div>
                                <strong class="text-slate-800 block text-xs">Ajak Keluar / Rehat Santai</strong>
                                <span class="text-[11px] text-slate-500">Mau ngopi bareng atau jalan santai buat ganti suasana.</span>
                            </div>
                        </label>

                        <label class="p-3 rounded-2xl border flex items-center gap-3 cursor-pointer transition" :class="supportType === 'quiet_presence' ? 'border-rose-500 bg-rose-50/50' : 'border-slate-200 hover:bg-slate-50'">
                            <input type="radio" name="support_type" value="quiet_presence" x-model="supportType" class="text-rose-600 focus:ring-rose-500">
                            <div>
                                <strong class="text-slate-800 block text-xs">Kehadiran Tenang (Quiet Presence)</strong>
                                <span class="text-[11px] text-slate-500">Cukup ditemani tanpa harus banyak ngobrol.</span>
                            </div>
                        </label>
                    </div>

                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Catatan Tambahan Singkat (Opsional)</label>
                        <input type="text" name="custom_short_note" x-model="customNote" maxlength="140" placeholder="Contoh: Lagi butuh distraction dari tugas..." class="w-full rounded-xl border-slate-200 text-xs focus:border-rose-500 focus:ring-rose-500">
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/60 text-[11px] text-slate-500">
                        🔒 Notifikasi terkirim aman ke {{ $members->count() }} orang di lingkaranmu tanpa memuat catatan pribadimu.
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="open = false" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 font-semibold">Batal</button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold shadow-md">Kirim Sinyal Sekarang</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Kontak -->
    <div x-data="{ open: false }" @open-add-member.window="open = true" x-show="open" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="open = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white rounded-3xl shadow-2xl p-6 w-full max-w-md border border-slate-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-slate-800">Tambah Kontak ke Lingkaran</h3>
                    <button @click="open = false" class="text-slate-400 hover:text-slate-600 text-lg">✕</button>
                </div>

                <form action="{{ route('circle.members.store') }}" method="POST" class="space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Nama Kontak</label>
                        <input type="text" name="name" required placeholder="Contoh: Dimas, Kak Rian, Nadia" class="w-full rounded-xl border-slate-200 text-xs focus:border-rose-500 focus:ring-rose-500">
                    </div>

                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Hubungan</label>
                        <select name="relationship_type" required class="w-full rounded-xl border-slate-200 text-xs focus:border-rose-500 focus:ring-rose-500">
                            <option value="sahabat">Sahabat Dekat</option>
                            <option value="keluarga">Keluarga</option>
                            <option value="pasangan">Pasangan</option>
                            <option value="mentor">Mentor / Dosen</option>
                            <option value="rekan">Rekan Kerja / Teman Kelas</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="font-semibold text-slate-700 block mb-1">No. WhatsApp/HP</label>
                            <input type="text" name="phone" placeholder="0812..." class="w-full rounded-xl border-slate-200 text-xs focus:border-rose-500 focus:ring-rose-500">
                        </div>
                        <div>
                            <label class="font-semibold text-slate-700 block mb-1">Email</label>
                            <input type="email" name="email" placeholder="nama@email.com" class="w-full rounded-xl border-slate-200 text-xs focus:border-rose-500 focus:ring-rose-500">
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="open = false" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 font-semibold">Batal</button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold shadow-md">Simpan Kontak</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

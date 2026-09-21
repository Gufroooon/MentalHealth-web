<x-app-layout>
    <x-slot name="header">
        <div>
            <span class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-nara-800 bg-nara-100 px-3 py-1 rounded-full border border-nara-200/60">
                <x-icon name="recovery" class="w-3.5 h-3.5 text-nara-700" />
                <span>Eksperimen Pemulihan</span>
            </span>
            <h2 class="font-black text-2xl sm:text-3xl text-slate-800 tracking-tight mt-1.5">
                Recovery Lab & Dynamic Recovery Profile
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                Laboratorium mandiri untuk melacak aktivitas mana yang terbukti paling efektif memulihkan energi dan suasana hatimu.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <button @click="$dispatch('open-recovery-modal')" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-nara-600 hover:bg-nara-700 text-white font-bold text-xs sm:text-sm shadow-md shadow-nara-600/20 transition transform active:scale-95">
                <x-icon name="plus" class="w-4 h-4 text-white" />
                <span>Catat Sesi Rehat Baru</span>
            </button>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8" x-data="{ tab: 'profile' }">

        <!-- Nav Tabs -->
        <div class="flex border-b border-slate-200 gap-4 text-xs sm:text-sm font-bold">
            <button @click="tab = 'profile'" :class="tab === 'profile' ? 'border-nara-600 text-nara-900 border-b-2 font-black' : 'border-transparent text-slate-500 hover:text-slate-700'" class="pb-3 px-2 flex items-center gap-2 transition">
                <x-icon name="sparkle" class="w-4 h-4" />
                <span>Profil Efektivitas Pemulihan</span>
            </button>
            <button @click="tab = 'catalog'" :class="tab === 'catalog' ? 'border-nara-600 text-nara-900 border-b-2 font-black' : 'border-transparent text-slate-500 hover:text-slate-700'" class="pb-3 px-2 flex items-center gap-2 transition">
                <x-icon name="recovery" class="w-4 h-4" />
                <span>Katalog Aktivitas Rehat</span>
            </button>
            <button @click="tab = 'history'" :class="tab === 'history' ? 'border-nara-600 text-nara-900 border-b-2 font-black' : 'border-transparent text-slate-500 hover:text-slate-700'" class="pb-3 px-2 flex items-center gap-2 transition">
                <x-icon name="clock" class="w-4 h-4" />
                <span>Riwayat Sesi Rehat ({{ $profileData['total_sessions_count'] }})</span>
            </button>
        </div>

        <!-- ========================================== -->
        <!-- TAB 1: DYNAMIC RECOVERY PROFILE (RANKING) -->
        <!-- ========================================== -->
        <div x-show="tab === 'profile'" class="space-y-6">

            <div class="bg-gradient-to-r from-[#345577] via-[#436B92] to-[#557FA8] rounded-3xl p-6 sm:p-8 text-white shadow-card border border-white/20">
                <div class="max-w-2xl">
                    <span class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-widest text-nara-100 bg-white/15 px-3 py-1 rounded-full border border-white/20 mb-2">
                        <x-icon name="sparkle" class="w-3.5 h-3.5 text-white" />
                        <span>Data-Driven Restoration</span>
                    </span>
                    <h3 class="text-xl sm:text-2xl font-black text-white mt-1 mb-2">Peringkat Aktivitas Pemulihan Personalmu</h3>
                    <p class="text-xs sm:text-sm text-white/85 leading-relaxed font-normal">
                        Setiap tubuh dan pikiran merespons rehat secara berbeda. Berikut adalah urutan aktivitas yang menghasilkan kenaikan energi (&Delta; Energy) dan mood (&Delta; Mood) tertinggi untukmu:
                    </p>
                </div>
            </div>

            <!-- Ranked Cards List -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach ($profileData['ranked_activities'] as $index => $act)
                    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-card hover:shadow-card-hover transition flex flex-col justify-between relative overflow-hidden">

                        <!-- Rank Badge -->
                        <div class="absolute top-5 right-5 w-8 h-8 rounded-full flex items-center justify-center font-black text-xs {{ $index === 0 ? 'bg-amber-100 text-amber-900 border border-amber-300' : ($index === 1 ? 'bg-slate-200 text-slate-700' : ($index === 2 ? 'bg-amber-50 text-amber-700' : 'bg-slate-100 text-slate-500')) }}">
                            #{{ $index + 1 }}
                        </div>

                        <div>
                            <div class="w-10 h-10 rounded-2xl bg-nara-50 text-nara-700 border border-nara-200 flex items-center justify-center font-bold mb-3.5">
                                <x-icon name="recovery" class="w-5 h-5 text-nara-600" />
                            </div>

                            <h4 class="font-black text-base text-slate-800 pr-10 mb-1">{{ $act['name'] }}</h4>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 bg-slate-100 px-2.5 py-0.5 rounded-full inline-block mb-3">
                                {{ ucfirst($act['category']) }} &bull; {{ $act['default_duration_min'] }} menit
                            </span>

                            <p class="text-xs text-slate-600 leading-relaxed mb-4">
                                {{ $act['description'] }}
                            </p>
                        </div>

                        <!-- Stats Delta -->
                        <div class="pt-4 border-t border-slate-100">
                            @if ($act['sessions_count'] > 0)
                                <div class="grid grid-cols-2 gap-2 text-center text-xs">
                                    <div class="bg-emerald-50 p-2.5 rounded-2xl border border-emerald-100">
                                        <span class="text-[10px] text-emerald-800 block font-bold">&Delta; Rata-rata Energi</span>
                                        <span class="text-base font-black text-emerald-700">+{{ $act['avg_energy_delta'] }}</span>
                                    </div>
                                    <div class="bg-nara-50 p-2.5 rounded-2xl border border-nara-100">
                                        <span class="text-[10px] text-nara-800 block font-bold">&Delta; Rata-rata Mood</span>
                                        <span class="text-base font-black text-nara-700">+{{ $act['avg_mood_delta'] }}</span>
                                    </div>
                                </div>
                                <span class="text-[10px] text-slate-400 text-center block mt-2 font-medium">Diuji dalam {{ $act['sessions_count'] }} sesi rehat</span>
                            @else
                                <div class="bg-[#F8F6F0] p-3 rounded-2xl border border-slate-200/60 text-center">
                                    <span class="text-xs text-slate-400 font-medium">Belum pernah diuji dalam sesi rehat</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

        </div>

        <!-- ========================================== -->
        <!-- TAB 2: CATALOG OF ACTIVITIES -->
        <!-- ========================================== -->
        <div x-show="tab === 'catalog'" class="space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h3 class="text-lg font-black text-slate-800">Katalog Aktivitas Rehat Standar & Kustom</h3>
                    <p class="text-xs text-slate-500">Pilihan aktivitas pemulihan berdurasi 5–30 menit yang siap kamu jalankan kapan saja.</p>
                </div>
                <button @click="$dispatch('open-activity-modal')" class="px-5 py-2.5 rounded-2xl bg-nara-600 hover:bg-nara-700 text-white text-xs font-bold transition flex items-center gap-1.5 self-start sm:self-auto shadow-md shadow-nara-600/20">
                    <x-icon name="plus" class="w-4 h-4" />
                    <span>Tambah Aktivitas Kustom</span>
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($allActivities as $activity)
                    <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-card flex items-start gap-4">
                        <div class="w-10 h-10 rounded-2xl bg-nara-50 text-nara-700 border border-nara-200 flex items-center justify-center font-bold flex-shrink-0">
                            <x-icon name="recovery" class="w-5 h-5 text-nara-600" />
                        </div>
                        <div class="flex-1 space-y-1">
                            <div class="flex items-center justify-between gap-1">
                                <h4 class="text-xs font-bold text-slate-800">{{ $activity->name }}</h4>
                                <span class="text-[10px] bg-slate-100 px-2.5 py-0.5 rounded-full text-slate-600 font-bold">{{ $activity->default_duration_min }}m</span>
                            </div>
                            <p class="text-xs text-slate-500 leading-relaxed">{{ $activity->description }}</p>
                            <span class="text-[10px] uppercase font-bold text-nara-700 bg-nara-50 px-2 py-0.5 rounded-md inline-block mt-1">
                                {{ $activity->category }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB 3: SESSION HISTORY -->
        <!-- ========================================== -->
        <div x-show="tab === 'history'" class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/80 shadow-card">
            <h3 class="text-lg font-black text-slate-800 mb-4">Riwayat Eksperimen Rehat</h3>

            <div class="divide-y divide-slate-100">
                @forelse ($profileData['recent_sessions'] as $session)
                    <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-3.5">
                            <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-sm flex-shrink-0">
                                <x-icon name="check" class="w-5 h-5 text-emerald-700" />
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-800">{{ $session->activity->name }}</h4>
                                <p class="text-xs text-slate-500">{{ $session->created_at->format('d M Y, H:i') }} &bull; Durasi: {{ $session->duration_minutes }} Menit</p>
                                @if ($session->notes)
                                    <p class="text-xs text-slate-600 italic mt-1">"{{ $session->notes }}"</p>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-3 text-xs">
                            <div class="bg-emerald-50 px-3.5 py-1.5 rounded-xl border border-emerald-100 text-center">
                                <span class="text-[10px] text-slate-400 block font-bold">Energi</span>
                                <span class="font-bold text-emerald-800">{{ $session->energy_before }} &rarr; {{ $session->energy_after }} (+{{ $session->energy_delta }})</span>
                            </div>
                            <div class="bg-nara-50 px-3.5 py-1.5 rounded-xl border border-nara-100 text-center">
                                <span class="text-[10px] text-slate-400 block font-bold">Mood</span>
                                <span class="font-bold text-nara-800">{{ $session->mood_before }} &rarr; {{ $session->mood_after }} (+{{ $session->mood_delta }})</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="py-8 text-xs text-slate-400 text-center">Belum ada sesi rehat yang dicatat. Yuk lakukan rehat 15 menit hari ini!</p>
                @endforelse
            </div>
        </div>

    </div>

    <!-- Modal Catat Sesi Rehat (Before vs After) -->
    <div x-data="{
        open: false,
        activityId: '{{ $allActivities->first()?->id ?? 1 }}',
        energyBefore: 45,
        energyAfter: 70,
        moodBefore: 50,
        moodAfter: 75,
        duration: 15,
        notes: ''
    }" @open-recovery-modal.window="open = true" x-show="open" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" @click="open = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white rounded-[2rem] shadow-2xl p-6 sm:p-7 w-full max-w-lg border border-slate-200">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <span class="text-xs font-bold uppercase text-nara-700">Self-Experiment Logger</span>
                        <h3 class="text-lg font-black text-slate-800">Catat Eksperimen Pemulihan</h3>
                    </div>
                    <button @click="open = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
                </div>

                <form action="{{ route('recovery.sessions.store') }}" method="POST" class="space-y-4 text-xs">
                    @csrf

                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Aktivitas Rehat yang Dilakukan</label>
                        <select name="activity_id" x-model="activityId" required class="w-full rounded-xl border-slate-200 text-xs focus:border-nara-500 focus:ring-nara-500 p-2.5">
                            @foreach ($allActivities as $act)
                                <option value="{{ $act->id }}">{{ $act->name }} ({{ $act->default_duration_min }}m)</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Durasi Rehat Sebenarnya (Menit)</label>
                        <input type="number" name="duration_minutes" x-model="duration" min="1" max="300" required class="w-full rounded-xl border-slate-200 text-xs focus:border-nara-500 focus:ring-nara-500 p-2.5">
                    </div>

                    <!-- Energy Before vs After -->
                    <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200/60 space-y-3">
                        <span class="font-bold text-emerald-950 block">Perubahan Tingkat Energi Fisik</span>

                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-slate-600">Energi SEBELUM Rehat</span>
                                <span class="font-bold text-slate-800" x-text="energyBefore + '/100'"></span>
                            </div>
                            <input type="range" name="energy_before" min="0" max="100" x-model="energyBefore" class="w-full accent-emerald-600">
                        </div>

                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-emerald-900 font-bold">Energi SESUDAH Rehat</span>
                                <span class="font-bold text-emerald-700" x-text="energyAfter + '/100'"></span>
                            </div>
                            <input type="range" name="energy_after" min="0" max="100" x-model="energyAfter" class="w-full accent-emerald-600">
                        </div>
                    </div>

                    <!-- Mood Before vs After -->
                    <div class="p-4 rounded-2xl bg-nara-50/70 border border-nara-200/60 space-y-3">
                        <span class="font-bold text-nara-950 block">Perubahan Suasana Hati (Mood)</span>

                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-slate-600">Mood SEBELUM Rehat</span>
                                <span class="font-bold text-slate-800" x-text="moodBefore + '/100'"></span>
                            </div>
                            <input type="range" name="mood_before" min="0" max="100" x-model="moodBefore" class="w-full accent-nara-600">
                        </div>

                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-nara-900 font-bold">Mood SESUDAH Rehat</span>
                                <span class="font-bold text-nara-700" x-text="moodAfter + '/100'"></span>
                            </div>
                            <input type="range" name="mood_after" min="0" max="100" x-model="moodAfter" class="w-full accent-nara-600">
                        </div>
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Catatan Eksperimen (Opsional)</label>
                        <textarea name="notes" x-model="notes" rows="2" placeholder="Gimana rasanya setelah rehat? Ada hal menarik yang dirasakan?" class="w-full rounded-xl border-slate-200 text-xs focus:border-nara-500 focus:ring-nara-500 p-2.5"></textarea>
                    </div>

                    <div class="flex justify-end gap-2.5 pt-2">
                        <button type="button" @click="open = false" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 font-bold">Batal</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-nara-600 hover:bg-nara-700 text-white font-bold shadow-md shadow-nara-600/20">Simpan Hasil Eksperimen</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Aktivitas Kustom -->
    <div x-data="{ open: false }" @open-activity-modal.window="open = true" x-show="open" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" @click="open = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white rounded-[2rem] shadow-2xl p-6 sm:p-7 w-full max-w-md border border-slate-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-black text-slate-800">Tambah Aktivitas Rehat Kustom</h3>
                    <button @click="open = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
                </div>

                <form action="{{ route('recovery.activities.store') }}" method="POST" class="space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Nama Aktivitas</label>
                        <input type="text" name="name" required placeholder="Contoh: Sketsa Gambar 10 Menit, Menyiram Tanaman" class="w-full rounded-xl border-slate-200 text-xs focus:border-nara-500 focus:ring-nara-500 p-2.5">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="font-bold text-slate-700 block mb-1">Kategori</label>
                            <select name="category" required class="w-full rounded-xl border-slate-200 text-xs focus:border-nara-500 focus:ring-nara-500 p-2.5">
                                <option value="physical">Fisik (Physical)</option>
                                <option value="mental">Mental (Pikiran)</option>
                                <option value="social">Sosial</option>
                                <option value="sensory">Sensorik / Musik</option>
                                <option value="creative">Kreatif</option>
                            </select>
                        </div>
                        <div>
                            <label class="font-bold text-slate-700 block mb-1">Durasi Default (Menit)</label>
                            <input type="number" name="default_duration_min" value="15" min="5" max="180" required class="w-full rounded-xl border-slate-200 text-xs focus:border-nara-500 focus:ring-nara-500 p-2.5">
                        </div>
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Deskripsi Singkat</label>
                        <textarea name="description" rows="2" placeholder="Cara menjalankan rehat ini..." class="w-full rounded-xl border-slate-200 text-xs focus:border-nara-500 focus:ring-nara-500 p-2.5"></textarea>
                    </div>

                    <div class="flex justify-end gap-2.5 pt-2">
                        <button type="button" @click="open = false" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 font-bold">Batal</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-nara-600 hover:bg-nara-700 text-white font-bold shadow-md shadow-nara-600/20">Simpan ke Katalog</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

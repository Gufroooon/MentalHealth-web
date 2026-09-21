<x-app-layout>
    <x-slot name="header">
        <div>
            <span class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-nara-800 bg-nara-100 px-3 py-1 rounded-full border border-nara-200/60">
                <x-icon name="pattern" class="w-3.5 h-3.5 text-nara-700" />
                <span>Analisis Pola & Simulasi</span>
            </span>
            <h2 class="font-black text-2xl sm:text-3xl text-slate-800 tracking-tight mt-1.5">
                Life Pattern Engine & Simulator "What If?"
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                Menemukan sebab-akibat antara agenda hidup dan sinyal tubuhmu, serta menguji potensi perbaikan kebiasaan.
            </p>
        </div>
        <div>
            <button @click="$dispatch('open-event-modal')" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-nara-600 hover:bg-nara-700 text-white font-bold text-xs sm:text-sm shadow-md shadow-nara-600/20 transition transform active:scale-95">
                <x-icon name="plus" class="w-4 h-4 text-white" />
                <span>Tambah Agenda / Deadline</span>
            </button>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8" x-data="{ tab: 'patterns' }">

        <!-- Nav Tabs -->
        <div class="flex border-b border-slate-200 gap-4 text-xs sm:text-sm font-bold">
            <button @click="tab = 'patterns'" :class="tab === 'patterns' ? 'border-nara-600 text-nara-900 border-b-2 font-black' : 'border-transparent text-slate-500 hover:text-slate-700'" class="pb-3 px-2 flex items-center gap-2 transition">
                <x-icon name="pattern" class="w-4 h-4" />
                <span>Pola Sebab-Akibat (Life Patterns)</span>
            </button>
            <button @click="tab = 'simulator'" :class="tab === 'simulator' ? 'border-nara-600 text-nara-900 border-b-2 font-black' : 'border-transparent text-slate-500 hover:text-slate-700'" class="pb-3 px-2 flex items-center gap-2 transition">
                <x-icon name="sparkle" class="w-4 h-4" />
                <span>Simulator Kebiasaan ("What If?")</span>
            </button>
        </div>

        <!-- ========================================== -->
        <!-- TAB 1: LIFE PATTERNS TIMELINE -->
        <!-- ========================================== -->
        <div x-show="tab === 'patterns'" class="space-y-6">

            <!-- SAVED / DETECTED PATTERNS -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                @forelse ($patternData['saved_patterns'] as $pattern)
                    <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/80 shadow-card hover:shadow-card-hover transition flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1 rounded-full bg-nara-50 text-nara-800 border border-nara-200">
                                    <x-icon name="pattern" class="w-3.5 h-3.5 text-nara-600" />
                                    <span>Pola Terdeteksi</span>
                                </span>
                                <span class="text-xs font-semibold text-slate-400">{{ $pattern->detected_at->format('d M Y') }}</span>
                            </div>

                            <h3 class="text-base sm:text-lg font-black text-slate-800 mb-2">{{ $pattern->title }}</h3>
                            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed mb-4">
                                {{ $pattern->description_json['summary'] ?? '' }}
                            </p>

                            <!-- Chain Steps Visual -->
                            @if (!empty($pattern->description_json['chain']))
                                <div class="bg-[#F8F6F0] rounded-2xl p-4 border border-slate-200/60 mb-4 space-y-2">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Rantai Sebab-Akibat:</span>
                                    <div class="flex flex-wrap items-center gap-2 text-xs">
                                        @foreach ($pattern->description_json['chain'] as $index => $step)
                                            <span class="px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-800 font-bold shadow-xs">
                                                {{ $step }}
                                            </span>
                                            @if (!$loop->last)
                                                <span class="text-nara-400 font-black">&rarr;</span>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        @if (!empty($pattern->description_json['recommendation']))
                            <div class="pt-3 border-t border-slate-100 flex items-start gap-2.5 text-xs text-emerald-900 bg-emerald-50/70 p-3.5 rounded-2xl border border-emerald-200/60">
                                <x-icon name="sparkle" class="w-4 h-4 text-emerald-600 flex-shrink-0 mt-0.5" />
                                <div>
                                    <strong class="font-bold text-emerald-800 block mb-0.5">Saran NARA:</strong>
                                    <span>{{ $pattern->description_json['recommendation'] }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="lg:col-span-2 bg-white rounded-3xl p-8 border border-slate-200 text-center space-y-3">
                        <div class="w-12 h-12 rounded-2xl bg-nara-50 text-nara-600 flex items-center justify-center mx-auto">
                            <x-icon name="pattern" class="w-6 h-6 text-nara-600" />
                        </div>
                        <h4 class="font-bold text-slate-800 text-base">Belum Ada Pola Negatif Berulang</h4>
                        <p class="text-xs text-slate-500 max-w-md mx-auto">Terus catat sinyal dan agenda harianmu agar NARA dapat memetakan korelasi hidupmu dengan akurat.</p>
                    </div>
                @endforelse
            </div>

            <!-- LIFE EVENTS LIST -->
            <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/80 shadow-card">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-black text-slate-800">Timeline Agenda & Beban Hidup</h3>
                        <p class="text-xs text-slate-500">Agenda besar (ujian, deadline, proyek) yang berpotensi mempengaruhi jam tidur dan tingkat stresmu.</p>
                    </div>
                    <button @click="$dispatch('open-event-modal')" class="text-xs font-bold text-nara-700 hover:underline flex items-center gap-1">
                        <x-icon name="plus" class="w-3.5 h-3.5" />
                        <span>Tambah Agenda</span>
                    </button>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse ($patternData['events'] as $event)
                        <div class="py-3.5 flex items-center justify-between gap-4 hover:bg-[#F8F6F0] px-3 rounded-2xl transition">
                            <div class="flex items-center gap-3.5">
                                <div class="w-10 h-10 rounded-2xl bg-nara-100 text-nara-700 flex items-center justify-center font-bold text-xs flex-shrink-0">
                                    <x-icon name="calendar" class="w-5 h-5 text-nara-700" />
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-800">{{ $event->title }}</h4>
                                    <div class="flex flex-wrap items-center gap-2 text-xs text-slate-500 mt-0.5">
                                        <span>Kategori: <strong class="capitalize text-slate-700 font-bold">{{ $event->category }}</strong></span>
                                        <span>&bull;</span>
                                        <span>Mulai: {{ $event->start_date->format('d M Y') }}</span>
                                        @if ($event->end_date)
                                            <span>- {{ $event->end_date->format('d M Y') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <span class="text-xs px-3 py-1 rounded-full font-bold bg-slate-100 text-slate-700 border border-slate-200/60">
                                    Dampak: {{ $event->severity_impact }}/5
                                </span>
                                <form action="{{ route('pattern.events.destroy', $event->id) }}" method="POST" onsubmit="return confirm('Hapus agenda ini dari timeline?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-rose-500 hover:text-rose-700 font-bold p-1">Hapus</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="py-6 text-xs text-slate-400 text-center">Belum ada agenda deadline/ujian yang dicatat.</p>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- ========================================== -->
        <!-- TAB 2: "WHAT IF?" HABIT SIMULATOR -->
        <!-- ========================================== -->
        <div x-show="tab === 'simulator'" class="space-y-6">

            <div class="bg-gradient-to-r from-[#345577] via-[#436B92] to-[#557FA8] rounded-3xl p-6 sm:p-8 text-white shadow-card relative overflow-hidden border border-white/20">
                <div class="max-w-2xl">
                    <span class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-widest text-nara-100 bg-white/15 px-3 py-1 rounded-full border border-white/20 mb-2">
                        <x-icon name="sparkle" class="w-3.5 h-3.5 text-white" />
                        <span>Deterministic Habit Simulator</span>
                    </span>
                    <h3 class="text-2xl sm:text-3xl font-black text-white mt-1 mb-2">Simulasi "What If?" Berbasis Datamu</h3>
                    <p class="text-xs sm:text-sm text-white/85 leading-relaxed font-normal">
                        Simulator ini menghitung estimasi hasil nyata berdasarkan data riwayat harimu sendiri saat kondisi tersebut pernah tercapai di masa lalu.
                    </p>
                </div>
            </div>

            <!-- Scenario Selection Pills -->
            <div class="flex flex-wrap gap-2.5">
                @foreach ($availableScenarios as $key => $label)
                    <a href="{{ route('pattern.index', ['scenario' => $key]) }}" class="px-4 py-2.5 rounded-2xl text-xs font-bold transition flex items-center gap-2 {{ $scenarioKey === $key ? 'bg-nara-600 text-white shadow-md shadow-nara-600/20' : 'bg-white text-slate-700 border border-slate-200 hover:bg-nara-50' }}">
                        <span>{{ $label }}</span>
                    </a>
                @endforeach
            </div>

            <!-- Simulation Result Card -->
            @if (!empty($simulationResult))
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    <!-- Main Comparison Graphic -->
                    <div class="lg:col-span-2 bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/80 shadow-card">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                            <div>
                                <h4 class="text-base sm:text-lg font-black text-slate-800">{{ $simulationResult['title'] ?? 'Skenario Tidak Tersedia' }}</h4>
                                <p class="text-xs text-slate-500">Target Metrik: <strong class="text-nara-700 font-bold">{{ $simulationResult['target_metric_label'] ?? '-' }}</strong></p>
                            </div>
                            <span class="text-xs px-3.5 py-1.5 rounded-full bg-nara-50 text-nara-800 font-black border border-nara-200 self-start sm:self-auto">
                                Potensi: {{ ($simulationResult['potential_delta'] ?? 0) > 0 ? '+' : '' }}{{ $simulationResult['potential_delta'] ?? 0 }} {{ $simulationResult['unit'] ?? '' }}
                            </span>
                        </div>

                        <p class="text-xs sm:text-sm text-slate-600 mb-6 leading-relaxed">
                            {{ $simulationResult['explanation'] ?? 'Belum cukup data historis untuk skenario ini.' }}
                        </p>

                        <!-- Comparative Bar Visual -->
                        <div class="space-y-4 bg-[#F8F6F0] p-5 rounded-2xl border border-slate-200/60">
                            <div>
                                <div class="flex justify-between text-xs font-semibold text-slate-600 mb-1.5">
                                    <span>Kondisi Rata-rata Saat Ini (Baseline)</span>
                                    <span class="font-bold text-slate-800">{{ $simulationResult['baseline_value'] ?? 0 }} {{ $simulationResult['unit'] ?? '' }}</span>
                                </div>
                                <div class="w-full bg-slate-200 rounded-full h-3 overflow-hidden">
                                    <div class="bg-slate-500 h-3 rounded-full" style="width: {{ min(100, $simulationResult['baseline_value'] ?? 0) }}%"></div>
                                </div>
                            </div>

                            <div>
                                <div class="flex justify-between text-xs font-semibold text-nara-900 mb-1.5">
                                    <span class="flex items-center gap-1.5 font-bold">
                                        <x-icon name="sparkle" class="w-3.5 h-3.5 text-nara-600" />
                                        <span>Potensi Skenario Baru</span>
                                    </span>
                                    <span class="font-black text-nara-700 text-sm">{{ $simulationResult['projected_value'] ?? 0 }} {{ $simulationResult['unit'] ?? '' }}</span>
                                </div>
                                <div class="w-full bg-nara-100 rounded-full h-3 overflow-hidden">
                                    <div class="bg-gradient-to-r from-nara-500 to-nara-700 h-3 rounded-full transition-all duration-700" style="width: {{ min(100, $simulationResult['projected_value'] ?? 0) }}%"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Medical Disclaimer -->
                        <div class="mt-5 p-3.5 rounded-2xl bg-amber-50 border border-amber-200/60 text-xs text-amber-900 flex items-center gap-2">
                            <x-icon name="heart" class="w-4 h-4 text-amber-700 flex-shrink-0" />
                            <span>{{ $simulationResult['disclaimer'] ?? 'Hasil simulasi bersifat estimasi, bukan saran medis.' }}</span>
                        </div>
                    </div>

                    <!-- Simulation Metadata & Historical Evidence -->
                    <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/80 shadow-card flex flex-col justify-between">
                        <div>
                            <h4 class="text-sm font-black text-slate-800 mb-3">Bukti Data Historis Kamu</h4>

                            <div class="space-y-3 text-xs">
                                <div class="p-4 rounded-2xl bg-[#F8F6F0] border border-slate-200/60">
                                    <span class="text-slate-400 block text-[10px] uppercase font-bold">Hari Cocok di Riwayat</span>
                                    <span class="text-2xl font-black text-slate-800">{{ $simulationResult['sample_days_count'] ?? 0 }} Hari</span>
                                    <span class="text-[11px] text-slate-500 block mt-0.5">dari total {{ $simulationResult['total_historical_days'] ?? 0 }} hari check-in</span>
                                </div>

                                <div class="p-4 rounded-2xl bg-[#F8F6F0] border border-slate-200/60">
                                    <span class="text-slate-400 block text-[10px] uppercase font-bold">Peningkatan Persentase</span>
                                    <span class="text-2xl font-black text-emerald-600">{{ ($simulationResult['potential_delta_percent'] ?? 0) > 0 ? '+' : '' }}{{ $simulationResult['potential_delta_percent'] ?? 0 }}%</span>
                                    <span class="text-[11px] text-slate-500 block mt-0.5">estimasi efisiensi energi mental</span>
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-100 mt-4">
                            <button @click="$dispatch('open-checkin')" class="w-full py-3 rounded-2xl bg-nara-600 hover:bg-nara-700 text-white text-xs font-bold text-center shadow-md shadow-nara-600/20 transition">
                                Terapkan Pada Check-in Hari Ini
                            </button>
                        </div>
                    </div>

                </div>
            @else
                <div class="bg-white rounded-3xl p-8 border border-slate-200 text-center">
                    <h4 class="font-bold text-slate-800 mb-1">Data Simulasi Belum Tersedia</h4>
                    <p class="text-xs text-slate-500">Belum cukup data check-in historis untuk skenario ini. Coba pilih skenario lain di atas.</p>
                </div>
            @endif

        </div>

    </div>

    <!-- Modal Tambah Agenda / Event -->
    <div x-data="{ open: false }" @open-event-modal.window="open = true" x-show="open" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" @click="open = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white rounded-[2rem] shadow-2xl p-6 sm:p-7 w-full max-w-lg border border-slate-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-black text-slate-800">Tambah Agenda / Deadline Hidup</h3>
                    <button @click="open = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
                </div>

                <form action="{{ route('pattern.events.store') }}" method="POST" class="space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Nama Agenda / Tugas</label>
                        <input type="text" name="title" required placeholder="Contoh: Deadline Tugas Besar, Ujian Akhir, Presentasi" class="w-full rounded-xl border-slate-200 text-xs focus:border-nara-500 focus:ring-nara-500 p-2.5">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="font-bold text-slate-700 block mb-1">Kategori</label>
                            <select name="category" required class="w-full rounded-xl border-slate-200 text-xs focus:border-nara-500 focus:ring-nara-500 p-2.5">
                                <option value="deadline">Deadline Tugas</option>
                                <option value="exam">Ujian / Tes</option>
                                <option value="work">Pekerjaan / Proyek</option>
                                <option value="relationship">Hubungan / Keluarga</option>
                                <option value="financial">Finansial</option>
                                <option value="health">Kesehatan</option>
                            </select>
                        </div>
                        <div>
                            <label class="font-bold text-slate-700 block mb-1">Tingkat Beban (1 - 5)</label>
                            <select name="severity_impact" required class="w-full rounded-xl border-slate-200 text-xs focus:border-nara-500 focus:ring-nara-500 p-2.5">
                                <option value="1">1 - Ringan</option>
                                <option value="2">2 - Sedang</option>
                                <option value="3" selected>3 - Cukup Menekan</option>
                                <option value="4">4 - Berat</option>
                                <option value="5">5 - Sangat Kritis</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="font-bold text-slate-700 block mb-1">Tanggal Mulai</label>
                            <input type="date" name="start_date" value="{{ date('Y-m-d') }}" required class="w-full rounded-xl border-slate-200 text-xs focus:border-nara-500 focus:ring-nara-500 p-2.5">
                        </div>
                        <div>
                            <label class="font-bold text-slate-700 block mb-1">Tanggal Berakhir (Opsional)</label>
                            <input type="date" name="end_date" class="w-full rounded-xl border-slate-200 text-xs focus:border-nara-500 focus:ring-nara-500 p-2.5">
                        </div>
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Catatan Tambahan</label>
                        <textarea name="notes" rows="2" placeholder="Catatan singkat tentang ekspektasi atau persiapan..." class="w-full rounded-xl border-slate-200 text-xs focus:border-nara-500 focus:ring-nara-500 p-2.5"></textarea>
                    </div>

                    <div class="flex justify-end gap-2.5 pt-2">
                        <button type="button" @click="open = false" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 font-bold">Batal</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-nara-600 hover:bg-nara-700 text-white font-bold shadow-md shadow-nara-600/20">Simpan Agenda</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

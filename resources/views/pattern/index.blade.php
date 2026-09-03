{{--
    Dokumentasi file: View Blade.

    Menjelaskan tanggung jawab file resources/views/pattern/index.blade.php serta hubungan data atau UI-nya dengan bagian aplikasi lain.
--}}
<x-app-layout>
    <x-slot name="header">
        <div>
            <span class="text-xs font-semibold uppercase tracking-wider text-purple-800 bg-purple-100 px-3 py-1 rounded-full">
                🔮 Analisis Pola & Simulasi
            </span>
            <h2 class="font-extrabold text-2xl text-slate-800 tracking-tight mt-1">
                Life Pattern Engine & Simulator "What If?"
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                Menemukan sebab-akibat antara agenda hidup dan sinyal tubuhmu, serta menguji potensi perbaikan kebiasaan.
            </p>
        </div>
        <div>
            <button @click="$dispatch('open-event-modal')" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-purple-700 hover:bg-purple-800 text-white font-bold text-sm shadow-md shadow-purple-700/20 transition transform active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Tambah Agenda / Deadline</span>
            </button>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8" x-data="{ tab: 'patterns' }">

        <!-- Nav Tabs -->
        <div class="flex border-b border-slate-200 gap-4 text-sm font-semibold">
            <button @click="tab = 'patterns'" :class="tab === 'patterns' ? 'border-purple-600 text-purple-900 border-b-2 font-bold' : 'text-slate-500 hover:text-slate-700'" class="pb-3 px-2 flex items-center gap-2 transition">
                <span>🔗 Pola Sebab-Akibat (Life Patterns)</span>
            </button>
            <button @click="tab = 'simulator'" :class="tab === 'simulator' ? 'border-purple-600 text-purple-900 border-b-2 font-bold' : 'text-slate-500 hover:text-slate-700'" class="pb-3 px-2 flex items-center gap-2 transition">
                <span>🎯 Simulator Kebiasaan ("What If?")</span>
            </button>
        </div>

        <!-- ========================================== -->
        <!-- TAB 1: LIFE PATTERNS TIMELINE -->
        <!-- ========================================== -->
        <div x-show="tab === 'patterns'" class="space-y-6">
            
            <!-- SAVED / DETECTED PATTERNS -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                @forelse ($patternData['saved_patterns'] as $pattern)
                    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-purple-50 text-purple-800 border border-purple-200">
                                    Pola Terdeteksi
                                </span>
                                <span class="text-xs text-slate-400">{{ $pattern->detected_at->format('d M Y') }}</span>
                            </div>

                            <h3 class="text-base font-bold text-slate-800 mb-2">{{ $pattern->title }}</h3>
                            <p class="text-xs text-slate-600 leading-relaxed mb-4">
                                {{ $pattern->description_json['summary'] ?? '' }}
                            </p>

                            <!-- Chain Steps Visual -->
                            @if (!empty($pattern->description_json['chain']))
                                <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 mb-4 space-y-2">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Rantai Sebab-Akibat:</span>
                                    <div class="flex flex-wrap items-center gap-1.5 text-xs">
                                        @foreach ($pattern->description_json['chain'] as $index => $step)
                                            <span class="px-2.5 py-1 rounded-xl bg-white border border-slate-200 text-slate-700 font-medium">
                                                {{ $step }}
                                            </span>
                                            @if (!$loop->last)
                                                <span class="text-slate-400 font-bold">&rarr;</span>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        @if (!empty($pattern->description_json['recommendation']))
                            <div class="pt-3 border-t border-slate-100 flex items-start gap-2 text-xs text-emerald-800 bg-emerald-50/60 p-3 rounded-2xl border border-emerald-200/50">
                                <span class="text-emerald-600 font-bold">💡 Saran NARA:</span>
                                <span>{{ $pattern->description_json['recommendation'] }}</span>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="lg:col-span-2 bg-white rounded-3xl p-8 border border-slate-200 text-center">
                        <span class="text-3xl block mb-2">🌱</span>
                        <h4 class="font-bold text-slate-800 mb-1">Belum Ada Pola Negatif Berulang</h4>
                        <p class="text-xs text-slate-500">Terus catat sinyal dan agenda harianmu agar NARA dapat memetakan korelasi hidupmu.</p>
                    </div>
                @endforelse
            </div>

            <!-- LIFE EVENTS LIST -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">Timeline Agenda & Beban Hidup</h3>
                        <p class="text-xs text-slate-500">Agenda besar (ujian, deadline, proyek) yang berpotensi mempengaruhi jam tidur dan tingkat stresmu.</p>
                    </div>
                    <button @click="$dispatch('open-event-modal')" class="text-xs font-bold text-purple-700 hover:underline">
                        + Tambah Agenda
                    </button>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse ($patternData['events'] as $event)
                        <div class="py-3.5 flex items-center justify-between gap-4 hover:bg-slate-50/50 px-2 rounded-xl transition">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-purple-100 text-purple-700 flex items-center justify-center font-bold text-sm">
                                    📅
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-800">{{ $event->title }}</h4>
                                    <div class="flex items-center gap-2 text-xs text-slate-500 mt-0.5">
                                        <span>Kategori: <strong class="capitalize text-slate-700">{{ $event->category }}</strong></span>
                                        <span>&bull;</span>
                                        <span>Mulai: {{ $event->start_date->format('d M Y') }}</span>
                                        @if ($event->end_date)
                                            <span>- {{ $event->end_date->format('d M Y') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <span class="text-xs px-2.5 py-1 rounded-full font-bold bg-slate-100 text-slate-600">
                                    Dampak: {{ $event->severity_impact }}/5
                                </span>
                                <form action="{{ route('pattern.events.destroy', $event->id) }}" method="POST" onsubmit="return confirm('Hapus agenda ini dari timeline?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-rose-500 hover:text-rose-700 font-medium">Hapus</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="py-4 text-xs text-slate-400 text-center">Belum ada agenda deadline/ujian yang dicatat.</p>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- ========================================== -->
        <!-- TAB 2: "WHAT IF?" HABIT SIMULATOR -->
        <!-- ========================================== -->
        <div x-show="tab === 'simulator'" class="space-y-6">
            
            <div class="bg-gradient-to-r from-purple-900 to-indigo-900 rounded-3xl p-6 text-white shadow-xl relative overflow-hidden">
                <div class="max-w-2xl">
                    <span class="text-xs font-bold uppercase tracking-widest text-purple-300">Deterministic Habit Simulator</span>
                    <h3 class="text-2xl font-extrabold text-white mt-1 mb-2">Simulasi "What If?" Berbasis Datamu</h3>
                    <p class="text-xs text-purple-100 leading-relaxed">
                        Simulator ini menghitung estimasi hasil nyata berdasarkan data riwayat harimu sendiri saat kondisi tersebut pernah tercapai di masa lalu.
                    </p>
                </div>
            </div>

            <!-- Scenario Selection Pills -->
            <div class="flex flex-wrap gap-2.5">
                @foreach ($availableScenarios as $key => $label)
                    <a href="{{ route('pattern.index', ['scenario' => $key]) }}" class="px-4 py-2.5 rounded-2xl text-xs font-bold transition flex items-center gap-2 {{ $scenarioKey === $key ? 'bg-purple-700 text-white shadow-md shadow-purple-700/20' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50' }}">
                        <span>{{ $label }}</span>
                    </a>
                @endforeach
            </div>

            <!-- Simulation Result Card -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Main Comparison Graphic -->
                <div class="lg:col-span-2 bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h4 class="text-lg font-bold text-slate-800">{{ $simulationResult['title'] }}</h4>
                            <p class="text-xs text-slate-500">Target Metrik: <strong class="text-purple-700">{{ $simulationResult['target_metric_label'] }}</strong></p>
                        </div>
                        <span class="text-xs px-3 py-1 rounded-full bg-purple-50 text-purple-800 font-bold border border-purple-200">
                            Potensi: {{ $simulationResult['potential_delta'] > 0 ? '+' : '' }}{{ $simulationResult['potential_delta'] }} {{ $simulationResult['unit'] }}
                        </span>
                    </div>

                    <p class="text-xs text-slate-600 mb-6 leading-relaxed">
                        {{ $simulationResult['explanation'] }}
                    </p>

                    <!-- Comparative Bar Visual -->
                    <div class="space-y-4 bg-slate-50 p-5 rounded-2xl border border-slate-100">
                        <div>
                            <div class="flex justify-between text-xs font-semibold text-slate-600 mb-1">
                                <span>Kondisi Rata-rata Saat Ini (Baseline)</span>
                                <span class="font-bold text-slate-800">{{ $simulationResult['baseline_value'] }} {{ $simulationResult['unit'] }}</span>
                            </div>
                            <div class="w-full bg-slate-200 rounded-full h-3 overflow-hidden">
                                <div class="bg-slate-500 h-3 rounded-full" style="width: {{ min(100, $simulationResult['baseline_value']) }}%"></div>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between text-xs font-semibold text-purple-900 mb-1">
                                <span class="flex items-center gap-1 font-bold">✨ Potensi Skenario Baru</span>
                                <span class="font-black text-purple-700 text-sm">{{ $simulationResult['projected_value'] }} {{ $simulationResult['unit'] }}</span>
                            </div>
                            <div class="w-full bg-purple-100 rounded-full h-3 overflow-hidden">
                                <div class="bg-gradient-to-r from-purple-600 to-indigo-600 h-3 rounded-full transition-all duration-700" style="width: {{ min(100, $simulationResult['projected_value']) }}%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Medical Disclaimer -->
                    <div class="mt-5 p-3 rounded-xl bg-amber-50 border border-amber-200/60 text-[11px] text-amber-900 flex items-center gap-2">
                        <span class="font-bold">⚠️ Catatan:</span>
                        <span>{{ $simulationResult['disclaimer'] }}</span>
                    </div>
                </div>

                <!-- Simulation Metadata & Historical Evidence -->
                <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm flex flex-col justify-between">
                    <div>
                        <h4 class="text-sm font-bold text-slate-800 mb-3">Bukti Data Historis Kamu</h4>
                        
                        <div class="space-y-3 text-xs">
                            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100">
                                <span class="text-slate-400 block text-[10px] uppercase font-bold">Hari Cocok di Riwayat</span>
                                <span class="text-xl font-extrabold text-slate-800">{{ $simulationResult['sample_days_count'] }} Hari</span>
                                <span class="text-[11px] text-slate-500 block">dari total {{ $simulationResult['total_historical_days'] }} hari check-in</span>
                            </div>

                            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100">
                                <span class="text-slate-400 block text-[10px] uppercase font-bold">Peningkatan Persentase</span>
                                <span class="text-xl font-extrabold text-emerald-600">{{ $simulationResult['potential_delta_percent'] > 0 ? '+' : '' }}{{ $simulationResult['potential_delta_percent'] }}%</span>
                                <span class="text-[11px] text-slate-500 block">estimasi efisiensi energi mental</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100">
                        <button @click="$dispatch('open-checkin')" class="w-full py-2.5 rounded-xl bg-purple-700 hover:bg-purple-800 text-white text-xs font-bold text-center transition">
                            Terapkan Pada Check-in Hari Ini
                        </button>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- Modal Tambah Agenda / Event -->
    <div x-data="{ open: false }" @open-event-modal.window="open = true" x-show="open" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="open = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white rounded-3xl shadow-2xl p-6 w-full max-w-lg border border-slate-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-slate-800">Tambah Agenda / Deadline Hidup</h3>
                    <button @click="open = false" class="text-slate-400 hover:text-slate-600 text-lg">✕</button>
                </div>

                <form action="{{ route('pattern.events.store') }}" method="POST" class="space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Nama Agenda / Tugas</label>
                        <input type="text" name="title" required placeholder="Contoh: Deadline Tugas Besar, Ujian Akhir, Presentasi" class="w-full rounded-xl border-slate-200 text-xs focus:border-purple-500 focus:ring-purple-500">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="font-semibold text-slate-700 block mb-1">Kategori</label>
                            <select name="category" required class="w-full rounded-xl border-slate-200 text-xs focus:border-purple-500 focus:ring-purple-500">
                                <option value="deadline">Deadline Tugas</option>
                                <option value="exam">Ujian / Tes</option>
                                <option value="work">Pekerjaan / Proyek</option>
                                <option value="relationship">Hubungan / Keluarga</option>
                                <option value="financial">Finansial</option>
                                <option value="health">Kesehatan</option>
                            </select>
                        </div>
                        <div>
                            <label class="font-semibold text-slate-700 block mb-1">Tingkat Beban (1 - 5)</label>
                            <select name="severity_impact" required class="w-full rounded-xl border-slate-200 text-xs focus:border-purple-500 focus:ring-purple-500">
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
                            <label class="font-semibold text-slate-700 block mb-1">Tanggal Mulai</label>
                            <input type="date" name="start_date" value="{{ date('Y-m-d') }}" required class="w-full rounded-xl border-slate-200 text-xs focus:border-purple-500 focus:ring-purple-500">
                        </div>
                        <div>
                            <label class="font-semibold text-slate-700 block mb-1">Tanggal Berakhir (Opsional)</label>
                            <input type="date" name="end_date" class="w-full rounded-xl border-slate-200 text-xs focus:border-purple-500 focus:ring-purple-500">
                        </div>
                    </div>

                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Catatan Tambahan</label>
                        <textarea name="notes" rows="2" placeholder="Catatan singkat tentang ekspektasi atau persiapan..." class="w-full rounded-xl border-slate-200 text-xs focus:border-purple-500 focus:ring-purple-500"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="open = false" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 font-semibold">Batal</button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-purple-700 hover:bg-purple-800 text-white font-bold shadow-md">Simpan Agenda</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

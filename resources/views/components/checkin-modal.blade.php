<div x-data="{
    open: false,
    tab: 'mind',
    date: '{{ date('Y-m-d') }}',
    
    // Mind Vector
    mood: {{ $latestCheckin?->signal?->mood_level ?? 70 }},
    focus: {{ $latestCheckin?->signal?->focus_level ?? 70 }},
    stress: {{ $latestCheckin?->signal?->stress_level ?? 30 }},
    overthink: {{ $latestCheckin?->signal?->overthinking_level ?? 30 }},
    
    // Body Vector
    sleepHours: {{ $latestCheckin?->signal?->sleep_hours ?? 7.0 }},
    sleepQuality: {{ $latestCheckin?->signal?->sleep_quality ?? 75 }},
    energy: {{ $latestCheckin?->signal?->energy_level ?? 70 }},
    activityMin: {{ $latestCheckin?->signal?->physical_activity_min ?? 20 }},
    
    // Social Vector
    socialInteraction: {{ $latestCheckin?->signal?->social_interaction_score ?? 70 }},
    loneliness: {{ $latestCheckin?->signal?->loneliness_score ?? 20 }},
    friction: {{ $latestCheckin?->signal?->relationship_friction_score ?? 10 }},
    
    // Life Vector
    workload: {{ $latestCheckin?->signal?->workload_score ?? 45 }},
    financialPressure: {{ $latestCheckin?->signal?->financial_pressure_score ?? 30 }},
    goalProgress: {{ $latestCheckin?->signal?->goal_progress_score ?? 65 }},
    
    notes: '{{ $latestCheckin?->notes ?? '' }}',

    get mindScore() {
        return Math.round((this.mood * 0.35) + (this.focus * 0.30) + ((100 - this.stress) * 0.20) + ((100 - this.overthink) * 0.15));
    },
    get bodyScore() {
        let sleepDurScore = Math.min(100, (this.sleepHours / 8.0) * 100);
        let actScore = Math.min(100, this.activityMin * 2.5);
        return Math.round((this.sleepQuality * 0.35) + (this.energy * 0.35) + (actScore * 0.15) + (sleepDurScore * 0.15));
    },
    get socialScore() {
        return Math.round((this.socialInteraction * 0.50) + ((100 - this.loneliness) * 0.30) + ((100 - this.friction) * 0.20));
    },
    get lifeScore() {
        return Math.round((this.goalProgress * 0.45) + ((100 - this.workload) * 0.30) + ((100 - this.financialPressure) * 0.25));
    },
    get overallScore() {
        return Math.round((this.mindScore + this.bodyScore + this.socialScore + this.lifeScore) / 4);
    }
}" 
@open-checkin.window="open = true" 
x-show="open" 
x-cloak 
class="fixed inset-0 z-50 overflow-y-auto"
aria-labelledby="modal-title" 
role="dialog" 
aria-modal="true">

    <!-- Backdrop -->
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="open = false"></div>

    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl border border-slate-200">
            
            <!-- Header with Live Score Preview -->
            <div class="bg-gradient-to-r from-emerald-800 to-teal-700 px-6 py-5 text-white flex items-center justify-between">
                <div>
                    <span class="inline-block text-xs font-semibold px-2.5 py-0.5 rounded-full bg-white/20 text-white mb-1">
                        Sinyal Hidup Harian
                    </span>
                    <h3 class="text-xl font-bold">Check-in Kondisi Harimu</h3>
                    <p class="text-xs text-emerald-100">Jujur pada diri sendiri, NARA mendengarkan tanpa menghakimi.</p>
                </div>
                <div class="text-right bg-white/10 px-4 py-2 rounded-2xl border border-white/20">
                    <span class="text-[10px] uppercase font-bold tracking-wider text-emerald-200 block">Skor Kesejahteraan</span>
                    <span class="text-2xl font-black text-white" x-text="overallScore"></span><span class="text-xs text-emerald-200">/100</span>
                </div>
            </div>

            <!-- Form -->
            <form action="{{ route('checkin.store') }}" method="POST">
                @csrf
                <input type="hidden" name="date" x-model="date">

                <!-- Vector Tabs -->
                <div class="flex border-b border-slate-200 bg-slate-50/70 px-6 pt-3 gap-2 overflow-x-auto text-sm font-semibold">
                    <button type="button" @click="tab = 'mind'" :class="tab === 'mind' ? 'border-amber-500 text-amber-800 bg-white shadow-sm' : 'border-transparent text-slate-500 hover:text-slate-700'" class="px-4 py-2.5 rounded-t-xl border-b-2 flex items-center gap-2 transition">
                        <span>🧠 Pikiran (Mind)</span>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-amber-100 text-amber-800" x-text="mindScore"></span>
                    </button>

                    <button type="button" @click="tab = 'body'" :class="tab === 'body' ? 'border-cyan-500 text-cyan-800 bg-white shadow-sm' : 'border-transparent text-slate-500 hover:text-slate-700'" class="px-4 py-2.5 rounded-t-xl border-b-2 flex items-center gap-2 transition">
                        <span>⚡ Tubuh (Body)</span>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-cyan-100 text-cyan-800" x-text="bodyScore"></span>
                    </button>

                    <button type="button" @click="tab = 'social'" :class="tab === 'social' ? 'border-purple-500 text-purple-800 bg-white shadow-sm' : 'border-transparent text-slate-500 hover:text-slate-700'" class="px-4 py-2.5 rounded-t-xl border-b-2 flex items-center gap-2 transition">
                        <span>👥 Sosial (Social)</span>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-purple-100 text-purple-800" x-text="socialScore"></span>
                    </button>

                    <button type="button" @click="tab = 'life'" :class="tab === 'life' ? 'border-rose-500 text-rose-800 bg-white shadow-sm' : 'border-transparent text-slate-500 hover:text-slate-700'" class="px-4 py-2.5 rounded-t-xl border-b-2 flex items-center gap-2 transition">
                        <span>🎯 Hidup (Life)</span>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-rose-100 text-rose-800" x-text="lifeScore"></span>
                    </button>
                </div>

                <div class="p-6 space-y-6 max-h-[60vh] overflow-y-auto">
                    
                    <!-- TAB 1: MIND -->
                    <div x-show="tab === 'mind'" class="space-y-5">
                        <div class="bg-amber-50/50 p-4 rounded-2xl border border-amber-200/60 flex items-center justify-between">
                            <span class="text-xs text-amber-900 font-medium">Vektor Pikiran mengukur beban mental, overthinking, fokus, dan mood batinmu.</span>
                        </div>

                        <!-- Mood Level -->
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <label class="font-semibold text-slate-700">Suasana Hati (Mood)</label>
                                <span class="font-bold text-amber-600" x-text="mood + '/100'"></span>
                            </div>
                            <input type="range" name="mood_level" min="0" max="100" x-model="mood" class="w-full accent-amber-500 h-2 bg-slate-200 rounded-lg cursor-pointer">
                            <div class="flex justify-between text-[11px] text-slate-400">
                                <span>Sedih / Murung</span>
                                <span>Biasa Saja</span>
                                <span>Ceria & Bersemangat</span>
                            </div>
                        </div>

                        <!-- Stress Level -->
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <label class="font-semibold text-slate-700">Tingkat Stres / Tekanan Batin</label>
                                <span class="font-bold text-rose-600" x-text="stress + '/100'"></span>
                            </div>
                            <input type="range" name="stress_level" min="0" max="100" x-model="stress" class="w-full accent-rose-500 h-2 bg-slate-200 rounded-lg cursor-pointer">
                            <div class="flex justify-between text-[11px] text-slate-400">
                                <span>Sangat Rileks</span>
                                <span>Sedang</span>
                                <span>Sangat Tertekan</span>
                            </div>
                        </div>

                        <!-- Overthinking -->
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <label class="font-semibold text-slate-700">Tingkat Overthinking (Pikiran Berputar)</label>
                                <span class="font-bold text-amber-700" x-text="overthink + '/100'"></span>
                            </div>
                            <input type="range" name="overthinking_level" min="0" max="100" x-model="overthink" class="w-full accent-amber-600 h-2 bg-slate-200 rounded-lg cursor-pointer">
                            <div class="flex justify-between text-[11px] text-slate-400">
                                <span>Pikiran Tenang</span>
                                <span>Kadang Melamun</span>
                                <span>Kepala Sangat Penuh</span>
                            </div>
                        </div>

                        <!-- Focus Level -->
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <label class="font-semibold text-slate-700">Kemampuan Fokus & Konsentrasi</label>
                                <span class="font-bold text-emerald-600" x-text="focus + '/100'"></span>
                            </div>
                            <input type="range" name="focus_level" min="0" max="100" x-model="focus" class="w-full accent-emerald-500 h-2 bg-slate-200 rounded-lg cursor-pointer">
                            <div class="flex justify-between text-[11px] text-slate-400">
                                <span>Mudah Terdireksi</span>
                                <span>Cukup Fokus</span>
                                <span>Sangat Tajam / Flow</span>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 2: BODY -->
                    <div x-show="tab === 'body'" class="space-y-5">
                        <div class="bg-cyan-50/50 p-4 rounded-2xl border border-cyan-200/60 flex items-center justify-between">
                            <span class="text-xs text-cyan-900 font-medium">Vektor Tubuh mengamati durasi tidur, kesegaran fisik, dan aktivitas gerakmu.</span>
                        </div>

                        <!-- Sleep Hours -->
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <label class="font-semibold text-slate-700">Durasi Tidur Semalam (Jam)</label>
                                <span class="font-bold text-cyan-600" x-text="sleepHours + ' Jam'"></span>
                            </div>
                            <input type="range" name="sleep_hours" min="0" max="12" step="0.5" x-model="sleepHours" class="w-full accent-cyan-500 h-2 bg-slate-200 rounded-lg cursor-pointer">
                            <div class="flex justify-between text-[11px] text-slate-400">
                                <span>< 4 Jam (Kurang)</span>
                                <span>7-8 Jam (Ideal)</span>
                                <span>> 10 Jam</span>
                            </div>
                        </div>

                        <!-- Sleep Quality -->
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <label class="font-semibold text-slate-700">Kualitas Tidur (Seberapa Nyenyak?)</label>
                                <span class="font-bold text-cyan-700" x-text="sleepQuality + '/100'"></span>
                            </div>
                            <input type="range" name="sleep_quality" min="0" max="100" x-model="sleepQuality" class="w-full accent-cyan-600 h-2 bg-slate-200 rounded-lg cursor-pointer">
                            <div class="flex justify-between text-[11px] text-slate-400">
                                <span>Gelisah / Sering Bangun</span>
                                <span>Cukup Nyenyak</span>
                                <span>Pulas & Bangun Segar</span>
                            </div>
                        </div>

                        <!-- Energy Level -->
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <label class="font-semibold text-slate-700">Tingkat Energi Fisik Saat Ini</label>
                                <span class="font-bold text-teal-600" x-text="energy + '/100'"></span>
                            </div>
                            <input type="range" name="energy_level" min="0" max="100" x-model="energy" class="w-full accent-teal-500 h-2 bg-slate-200 rounded-lg cursor-pointer">
                            <div class="flex justify-between text-[11px] text-slate-400">
                                <span>Baterai Habis / Lesu</span>
                                <span>Sedang</span>
                                <span>Bertenaga Penuh</span>
                            </div>
                        </div>

                        <!-- Physical Activity -->
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <label class="font-semibold text-slate-700">Aktivitas Fisik / Olahraga (Menit)</label>
                                <span class="font-bold text-emerald-600" x-text="activityMin + ' Menit'"></span>
                            </div>
                            <input type="range" name="physical_activity_min" min="0" max="120" step="5" x-model="activityMin" class="w-full accent-emerald-500 h-2 bg-slate-200 rounded-lg cursor-pointer">
                            <div class="flex justify-between text-[11px] text-slate-400">
                                <span>Hanya Duduk / Rebahan</span>
                                <span>Jalan Santai 15-30m</span>
                                <span>Olahraga Intensif > 60m</span>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 3: SOCIAL -->
                    <div x-show="tab === 'social'" class="space-y-5">
                        <div class="bg-purple-50/50 p-4 rounded-2xl border border-purple-200/60 flex items-center justify-between">
                            <span class="text-xs text-purple-900 font-medium">Vektor Sosial memetakan kehangatan interaksi dan rasa keterhubunganmu.</span>
                        </div>

                        <!-- Social Interaction Score -->
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <label class="font-semibold text-slate-700">Kualitas Interaksi Sosial Hari Ini</label>
                                <span class="font-bold text-purple-600" x-text="socialInteraction + '/100'"></span>
                            </div>
                            <input type="range" name="social_interaction_score" min="0" max="100" x-model="socialInteraction" class="w-full accent-purple-500 h-2 bg-slate-200 rounded-lg cursor-pointer">
                            <div class="flex justify-between text-[11px] text-slate-400">
                                <span>Menguras Tenaga / Sepi</span>
                                <span>Interaksi Biasa</span>
                                <span>Sangat Hangat & Mengisi Energi</span>
                            </div>
                        </div>

                        <!-- Loneliness Score -->
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <label class="font-semibold text-slate-700">Rasa Terisolasi / Kesepian</label>
                                <span class="font-bold text-rose-600" x-text="loneliness + '/100'"></span>
                            </div>
                            <input type="range" name="loneliness_score" min="0" max="100" x-model="loneliness" class="w-full accent-rose-500 h-2 bg-slate-200 rounded-lg cursor-pointer">
                            <div class="flex justify-between text-[11px] text-slate-400">
                                <span>Merasa Ditemani</span>
                                <span>Kadang Sepi</span>
                                <span>Sangat Terasing</span>
                            </div>
                        </div>

                        <!-- Relationship Friction -->
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <label class="font-semibold text-slate-700">Gesekan / Konflik dengan Orang Lain</label>
                                <span class="font-bold text-amber-700" x-text="friction + '/100'"></span>
                            </div>
                            <input type="range" name="relationship_friction_score" min="0" max="100" x-model="friction" class="w-full accent-amber-600 h-2 bg-slate-200 rounded-lg cursor-pointer">
                            <div class="flex justify-between text-[11px] text-slate-400">
                                <span>Harmonis / Rukun</span>
                                <span>Salah Paham Kecil</span>
                                <span>Konflik Berat / Emosional</span>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 4: LIFE -->
                    <div x-show="tab === 'life'" class="space-y-5">
                        <div class="bg-rose-50/50 p-4 rounded-2xl border border-rose-200/60 flex items-center justify-between">
                            <span class="text-xs text-rose-900 font-medium">Vektor Hidup memantau beban studi/kerja, finansial, dan tujuan personalmu.</span>
                        </div>

                        <!-- Workload Score -->
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <label class="font-semibold text-slate-700">Beban Tugas / Pekerjaan Hari Ini</label>
                                <span class="font-bold text-rose-600" x-text="workload + '/100'"></span>
                            </div>
                            <input type="range" name="workload_score" min="0" max="100" x-model="workload" class="w-full accent-rose-500 h-2 bg-slate-200 rounded-lg cursor-pointer">
                            <div class="flex justify-between text-[11px] text-slate-400">
                                <span>Senggang / Santai</span>
                                <span>Cukup Padat</span>
                                <span>Overload / Deadline Mepet</span>
                            </div>
                        </div>

                        <!-- Financial Pressure -->
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <label class="font-semibold text-slate-700">Kekhawatiran Finansial / Pengeluaran</label>
                                <span class="font-bold text-amber-600" x-text="financialPressure + '/100'"></span>
                            </div>
                            <input type="range" name="financial_pressure_score" min="0" max="100" x-model="financialPressure" class="w-full accent-amber-500 h-2 bg-slate-200 rounded-lg cursor-pointer">
                            <div class="flex justify-between text-[11px] text-slate-400">
                                <span>Tenang & Aman</span>
                                <span>Sedang</span>
                                <span>Sangat Mencemaskan</span>
                            </div>
                        </div>

                        <!-- Goal Progress -->
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <label class="font-semibold text-slate-700">Progres Menuju Impian / Goal Personal</label>
                                <span class="font-bold text-emerald-600" x-text="goalProgress + '/100'"></span>
                            </div>
                            <input type="range" name="goal_progress_score" min="0" max="100" x-model="goalProgress" class="w-full accent-emerald-500 h-2 bg-slate-200 rounded-lg cursor-pointer">
                            <div class="flex justify-between text-[11px] text-slate-400">
                                <span>Merasa Macet</span>
                                <span>Ada Langkah Kecil</span>
                                <span>Puas dengan Progres Hari Ini</span>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="space-y-2 pt-2 border-t border-slate-100">
                            <label class="font-semibold text-slate-700 text-sm">Catatan Singkat (Opsional)</label>
                            <textarea name="notes" x-model="notes" rows="2" placeholder="Tuliskan 1 kalimat yang mewakili harimu..." class="w-full rounded-2xl border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                        </div>
                    </div>

                </div>

                <!-- Footer Actions -->
                <div class="bg-slate-50 px-6 py-4 border-t border-slate-200 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <button type="button" @click="tab = tab === 'mind' ? 'body' : (tab === 'body' ? 'social' : (tab === 'social' ? 'life' : 'mind'))" class="text-xs font-semibold text-slate-600 hover:text-emerald-700">
                            Tab Selanjutnya &rarr;
                        </button>
                    </div>
                    <div class="flex items-center gap-3">
                        <button type="button" @click="open = false" class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-200/70 transition">
                            Batal
                        </button>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-bold shadow-md shadow-emerald-700/20 transition flex items-center gap-2">
                            <span>Simpan Sinyal Hidup</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

{{--
    Modal interaktif untuk mengirim sinyal Mind, Body, Social, dan Life.
    Nilai awal diambil dari check-in terbaru agar form dapat dipakai untuk
    memperbarui data hari ini, bukan hanya membuat data baru.
--}}

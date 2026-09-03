<x-app-layout>
    <x-slot name="header">
        <div>
            <span class="text-xs font-semibold uppercase tracking-wider text-emerald-800 bg-emerald-100 px-3 py-1 rounded-full">
                🌿 Sinyal Hidup Hari Ini
            </span>
            <h2 class="font-extrabold text-2xl text-slate-800 tracking-tight mt-1">
                Halo, {{ explode(' ', $user->name)[0] }}! Gimana ritme hidupmu hari ini?
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }} &bull; Sistem pemulihan personal yang memahami pola, bukan sekadar suasana hati.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <button @click="$dispatch('open-checkin')" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-sm shadow-md shadow-emerald-700/20 transition transform active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>{{ $latestCheckin && $latestCheckin->date->isToday() ? 'Perbarui Check-in' : 'Catat Sinyal Hari Ini' }}</span>
            </button>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8" x-data="{ currentTab: 'overview' }">

        <!-- ========================================== -->
        <!-- MODULE 2: "WHAT CHANGED?" ENGINE NOTIFICATION BANNER -->
        <!-- ========================================== -->
        @if ($weeklyAnalysis['has_comparison'] && !empty($weeklyAnalysis['alerts']))
            <div class="bg-gradient-to-r from-slate-900 to-slate-800 rounded-3xl p-6 text-white shadow-xl border border-slate-700/60 relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-emerald-500/10 rounded-full blur-2xl pointer-events-none"></div>
                
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-500/20 border border-emerald-400/30 flex items-center justify-center text-emerald-400 text-xl font-bold">
                            🔍
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold uppercase tracking-widest text-emerald-400">What Changed Engine</span>
                                <span class="text-[10px] bg-slate-700 text-slate-300 px-2 py-0.5 rounded-full border border-slate-600">&Delta; &ge; 15%</span>
                            </div>
                            <h3 class="text-lg font-bold text-white">Perubahan Signifikan Terdeteksi Minggu Ini</h3>
                        </div>
                    </div>
                    <span class="text-xs text-slate-400">Perbandingan 7 hari terakhir vs 7 hari sebelumnya</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach ($weeklyAnalysis['alerts'] as $alert)
                        <div class="bg-slate-800/80 rounded-2xl p-4 border border-slate-700 hover:border-slate-600 transition">
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <span class="font-semibold text-sm text-slate-100">{{ $alert['title'] }}</span>
                                <span class="text-xs px-2.5 py-0.5 rounded-full font-bold {{ $alert['is_positive'] ? 'bg-emerald-950 text-emerald-300 border border-emerald-700/50' : 'bg-rose-950 text-rose-300 border border-rose-700/50' }}">
                                    {{ $alert['delta_percent'] > 0 ? '+' : '' }}{{ $alert['delta_percent'] }}%
                                </span>
                            </div>
                            <p class="text-xs text-slate-300 leading-relaxed">{{ $alert['insight'] }}</p>
                            <div class="mt-3 flex items-center justify-between text-[11px] text-slate-400 pt-2 border-t border-slate-700/50">
                                <span>Sebelumnya: <strong class="text-slate-200">{{ $alert['previous'] }}</strong></span>
                                <span>Saat ini: <strong class="text-slate-200">{{ $alert['current'] }}</strong></span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- ========================================== -->
        <!-- MODULE 5: "ONE SMALL THING" MICRO-ACTION & QUICK RECOVERY -->
        <!-- ========================================== -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- ONE SMALL THING CARD -->
            <div class="lg:col-span-2 bg-gradient-to-br from-emerald-800 to-teal-900 rounded-3xl p-6 text-white shadow-lg relative overflow-hidden flex flex-col justify-between"
                 x-data="{ completed: {{ $microAction['is_completed'] ? 'true' : 'false' }}, loading: false }">
                <div class="absolute -right-8 -top-8 w-40 h-40 bg-white/10 rounded-full blur-xl pointer-events-none"></div>

                <div>
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-7 h-7 rounded-xl bg-white/20 flex items-center justify-center text-sm">✨</span>
                            <span class="text-xs font-bold uppercase tracking-wider text-emerald-200">One Small Thing &bull; Aksi Mikro Hari Ini</span>
                        </div>
                        <span class="text-[11px] bg-white/10 text-emerald-100 px-2.5 py-0.5 rounded-full border border-white/20">
                            Fokus: {{ ucfirst($microAction['category']) }}
                        </span>
                    </div>

                    <h4 class="text-lg sm:text-xl font-bold text-white mb-2 leading-snug" :class="completed ? 'line-through opacity-75' : ''">
                        "{{ $microAction['title'] }}"
                    </h4>
                    
                    <p class="text-xs text-emerald-100/90 leading-relaxed mb-6">
                        @if ($lowestVector)
                            Disesuaikan secara otomatis dari sinyal terendahmu hari ini (<strong>{{ $lowestVector['details']['name'] }}</strong>: {{ $lowestVector['details']['score'] }} poin). Satu langkah kecil bergesekan rendah untuk memulihkan ritmemu.
                        @else
                            Satu langkah kecil bergesekan rendah untuk menjaga kestabilan harimu.
                        @endif
                    </p>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-white/20">
                    <span class="text-xs text-emerald-200" x-text="completed ? '🎉 Keren! Aksi mikro hari ini sudah selesai.' : 'Yuk selesaikan sebelum malam ini.'"></span>
                    
                    <form action="{{ route('micro-action.toggle', $microAction['id']) }}" method="POST" @submit.prevent="
                        loading = true;
                        fetch('{{ route('micro-action.toggle', $microAction['id']) }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            completed = data.is_completed;
                            loading = false;
                        });
                    ">
                        @csrf
                        <button type="submit" :disabled="loading" class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2"
                                :class="completed ? 'bg-white/20 hover:bg-white/30 text-white' : 'bg-white text-emerald-900 hover:bg-emerald-50 shadow-md'">
                            <span x-text="completed ? '✓ Selesai (Klik Batal)' : '✓ Tandai Selesai'"></span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- RECOVERY QUICK CARD -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-7 h-7 rounded-xl bg-teal-50 text-teal-700 flex items-center justify-center text-sm font-bold">🧪</span>
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Recovery Lab</span>
                        </div>
                        <a href="{{ route('recovery.index') }}" class="text-xs font-semibold text-emerald-700 hover:underline">Lihat Semua &rarr;</a>
                    </div>

                    <h4 class="text-base font-bold text-slate-800 mb-1">Aktivitas Pemulihan Terbaikmu</h4>
                    <p class="text-xs text-slate-500 mb-4">Berdasarkan ranking eksperimen pemulihanmu:</p>

                    <div class="space-y-2.5">
                        @forelse (array_slice($recoveryData['ranked_activities'], 0, 2) as $act)
                            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-sm font-bold">
                                        🌱
                                    </div>
                                    <div>
                                        <h5 class="text-xs font-bold text-slate-800">{{ $act['name'] }}</h5>
                                        <span class="text-[11px] text-slate-500">{{ $act['default_duration_min'] }} menit</span>
                                    </div>
                                </div>
                                <span class="text-xs font-black text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-lg border border-emerald-200/60">
                                    +{{ $act['avg_energy_delta'] ?? 15 }} Energi
                                </span>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400">Belum ada eksperimen pemulihan dicatat.</p>
                        @endforelse
                    </div>
                </div>

                <a href="{{ route('recovery.index') }}" class="mt-4 w-full py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200/80 text-center text-xs font-bold text-slate-700 transition block">
                    Mulai Eksperimen Rehat 15 Menit
                </a>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- MODULE 1: 4 LIFE SIGNAL VECTORS -->
        <!-- ========================================== -->
        <div>
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-xl font-bold text-slate-800 tracking-tight">4 Vektor Sinyal Hidup</h3>
                    <p class="text-xs text-slate-500">Kondisi kesehatan holistik dari dimensi mental, fisik, sosial, dan beban hidup.</p>
                </div>
                @if ($latestCheckin)
                    <span class="text-xs px-3 py-1 rounded-full font-bold bg-slate-100 text-slate-600 border border-slate-200">
                        Tag: {{ $latestCheckin->primary_tag }}
                    </span>
                @endif
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                
                <!-- VECTOR 1: MIND -->
                <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-sm hover:shadow-md transition">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-8 h-8 rounded-2xl bg-amber-100 text-amber-800 flex items-center justify-center text-sm font-bold">🧠</span>
                            <h4 class="font-bold text-sm text-slate-800">Pikiran (Mind)</h4>
                        </div>
                        <span class="text-lg font-extrabold text-amber-600">{{ $latestCheckin ? round($latestCheckin->mind_score) : 70 }}</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2 mb-4 overflow-hidden">
                        <div class="bg-amber-500 h-2 rounded-full transition-all duration-500" style="width: {{ $latestCheckin ? $latestCheckin->mind_score : 70 }}%"></div>
                    </div>
                    <div class="space-y-2 text-xs text-slate-600">
                        <div class="flex justify-between">
                            <span class="text-slate-400">Suasana Hati (Mood)</span>
                            <span class="font-semibold text-slate-700">{{ $latestCheckin?->signal?->mood_level ?? 70 }}%</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Kemampuan Fokus</span>
                            <span class="font-semibold text-slate-700">{{ $latestCheckin?->signal?->focus_level ?? 70 }}%</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Tingkat Stres</span>
                            <span class="font-semibold {{ ($latestCheckin?->signal?->stress_level ?? 30) > 60 ? 'text-rose-600' : 'text-slate-700' }}">{{ $latestCheckin?->signal?->stress_level ?? 30 }}%</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Overthinking</span>
                            <span class="font-semibold text-slate-700">{{ $latestCheckin?->signal?->overthinking_level ?? 30 }}%</span>
                        </div>
                    </div>
                </div>

                <!-- VECTOR 2: BODY -->
                <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-sm hover:shadow-md transition">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-8 h-8 rounded-2xl bg-cyan-100 text-cyan-800 flex items-center justify-center text-sm font-bold">⚡</span>
                            <h4 class="font-bold text-sm text-slate-800">Tubuh (Body)</h4>
                        </div>
                        <span class="text-lg font-extrabold text-cyan-600">{{ $latestCheckin ? round($latestCheckin->body_score) : 70 }}</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2 mb-4 overflow-hidden">
                        <div class="bg-cyan-500 h-2 rounded-full transition-all duration-500" style="width: {{ $latestCheckin ? $latestCheckin->body_score : 70 }}%"></div>
                    </div>
                    <div class="space-y-2 text-xs text-slate-600">
                        <div class="flex justify-between">
                            <span class="text-slate-400">Durasi Tidur</span>
                            <span class="font-semibold text-slate-700">{{ $latestCheckin?->signal?->sleep_hours ?? 7.0 }} Jam</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Kualitas Tidur</span>
                            <span class="font-semibold text-slate-700">{{ $latestCheckin?->signal?->sleep_quality ?? 75 }}%</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Tingkat Energi</span>
                            <span class="font-semibold text-slate-700">{{ $latestCheckin?->signal?->energy_level ?? 70 }}%</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Aktivitas Fisik</span>
                            <span class="font-semibold text-slate-700">{{ $latestCheckin?->signal?->physical_activity_min ?? 20 }} Menit</span>
                        </div>
                    </div>
                </div>

                <!-- VECTOR 3: SOCIAL -->
                <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-sm hover:shadow-md transition">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-8 h-8 rounded-2xl bg-purple-100 text-purple-800 flex items-center justify-center text-sm font-bold">👥</span>
                            <h4 class="font-bold text-sm text-slate-800">Sosial (Social)</h4>
                        </div>
                        <span class="text-lg font-extrabold text-purple-600">{{ $latestCheckin ? round($latestCheckin->social_score) : 70 }}</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2 mb-4 overflow-hidden">
                        <div class="bg-purple-500 h-2 rounded-full transition-all duration-500" style="width: {{ $latestCheckin ? $latestCheckin->social_score : 70 }}%"></div>
                    </div>
                    <div class="space-y-2 text-xs text-slate-600">
                        <div class="flex justify-between">
                            <span class="text-slate-400">Kualitas Interaksi</span>
                            <span class="font-semibold text-slate-700">{{ $latestCheckin?->signal?->social_interaction_score ?? 70 }}%</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Rasa Kesepian</span>
                            <span class="font-semibold {{ ($latestCheckin?->signal?->loneliness_score ?? 20) > 50 ? 'text-rose-600' : 'text-slate-700' }}">{{ $latestCheckin?->signal?->loneliness_score ?? 20 }}%</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Gesekan Hubungan</span>
                            <span class="font-semibold text-slate-700">{{ $latestCheckin?->signal?->relationship_friction_score ?? 10 }}%</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Dukungan Lingkaran</span>
                            <span class="font-semibold text-emerald-600">Aktif</span>
                        </div>
                    </div>
                </div>

                <!-- VECTOR 4: LIFE -->
                <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-sm hover:shadow-md transition">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-8 h-8 rounded-2xl bg-rose-100 text-rose-800 flex items-center justify-center text-sm font-bold">🎯</span>
                            <h4 class="font-bold text-sm text-slate-800">Hidup (Life)</h4>
                        </div>
                        <span class="text-lg font-extrabold text-rose-600">{{ $latestCheckin ? round($latestCheckin->life_score) : 70 }}</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2 mb-4 overflow-hidden">
                        <div class="bg-rose-500 h-2 rounded-full transition-all duration-500" style="width: {{ $latestCheckin ? $latestCheckin->life_score : 70 }}%"></div>
                    </div>
                    <div class="space-y-2 text-xs text-slate-600">
                        <div class="flex justify-between">
                            <span class="text-slate-400">Beban Kerja/Tugas</span>
                            <span class="font-semibold {{ ($latestCheckin?->signal?->workload_score ?? 40) > 70 ? 'text-rose-600' : 'text-slate-700' }}">{{ $latestCheckin?->signal?->workload_score ?? 40 }}%</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Tekanan Finansial</span>
                            <span class="font-semibold text-slate-700">{{ $latestCheckin?->signal?->financial_pressure_score ?? 30 }}%</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Progres Tujuan</span>
                            <span class="font-semibold text-emerald-600">{{ $latestCheckin?->signal?->goal_progress_score ?? 65 }}%</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Status Beban</span>
                            <span class="font-semibold text-slate-700">{{ ($latestCheckin?->signal?->workload_score ?? 40) > 75 ? 'Tinggi' : 'Terkendali' }}</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- ========================================== -->
        <!-- 14-DAY SIGNAL TRENDS CHART -->
        <!-- ========================================== -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div>
                    <h3 class="text-lg font-bold text-slate-800">Tren Fluktuasi Sinyal Hidup 14 Hari</h3>
                    <p class="text-xs text-slate-500">Melihat korelasi antara tidur, beban kerja, dan energimu dari waktu ke waktu.</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full bg-amber-50 text-amber-800 border border-amber-200">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span> Mind
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full bg-cyan-50 text-cyan-800 border border-cyan-200">
                        <span class="w-2 h-2 rounded-full bg-cyan-500"></span> Body
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full bg-purple-50 text-purple-800 border border-purple-200">
                        <span class="w-2 h-2 rounded-full bg-purple-500"></span> Social
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full bg-rose-50 text-rose-800 border border-rose-200">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span> Life
                    </span>
                </div>
            </div>

            <div class="h-72 w-full">
                <canvas id="signalsChart"></canvas>
            </div>
        </div>

    </div>

    <!-- Check-in Modal -->
    <x-checkin-modal :latestCheckin="$latestCheckin" />

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const ctx = document.getElementById('signalsChart')?.getContext('2d');
            if (!ctx) return;

            const chartData = @json($signalHistory);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartData.labels,
                    datasets: [
                        {
                            label: 'Pikiran (Mind)',
                            data: chartData.mind,
                            borderColor: '#F59E0B',
                            backgroundColor: 'rgba(245, 158, 11, 0.05)',
                            borderWidth: 2.5,
                            tension: 0.35,
                            pointRadius: 3,
                            pointHoverRadius: 6,
                        },
                        {
                            label: 'Tubuh (Body)',
                            data: chartData.body,
                            borderColor: '#06B6D4',
                            backgroundColor: 'rgba(6, 182, 212, 0.05)',
                            borderWidth: 2.5,
                            tension: 0.35,
                            pointRadius: 3,
                            pointHoverRadius: 6,
                        },
                        {
                            label: 'Sosial (Social)',
                            data: chartData.social,
                            borderColor: '#8B5CF6',
                            backgroundColor: 'rgba(139, 92, 246, 0.05)',
                            borderWidth: 2.5,
                            tension: 0.35,
                            pointRadius: 3,
                            pointHoverRadius: 6,
                        },
                        {
                            label: 'Hidup (Life)',
                            data: chartData.life,
                            borderColor: '#F43F5E',
                            backgroundColor: 'rgba(244, 63, 94, 0.05)',
                            borderWidth: 2.5,
                            tension: 0.35,
                            pointRadius: 3,
                            pointHoverRadius: 6,
                        },
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    scales: {
                        y: {
                            min: 0,
                            max: 100,
                            grid: {
                                color: '#F1F5F9'
                            },
                            ticks: {
                                font: { family: 'Plus Jakarta Sans', size: 11 },
                                callback: val => val + ' pt'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: { family: 'Plus Jakarta Sans', size: 11 }
                            }
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1E293B',
                            padding: 12,
                            titleFont: { family: 'Plus Jakarta Sans', size: 12, weight: 'bold' },
                            bodyFont: { family: 'Plus Jakarta Sans', size: 11 },
                            cornerRadius: 12,
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</x-app-layout>

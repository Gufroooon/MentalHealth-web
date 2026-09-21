<x-app-layout>
    <x-slot name="header">
        <div>
            <span class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-nara-800 bg-nara-100 px-3 py-1 rounded-full border border-nara-200/60">
                <x-icon name="sparkle" class="w-3.5 h-3.5 text-nara-600" />
                <span>Sinyal Hidup Hari Ini</span>
            </span>
            <h2 class="font-black text-2xl sm:text-3xl text-slate-800 tracking-tight mt-1.5">
                Halo, {{ explode(' ', $user->name)[0] }}! Gimana ritme hidupmu hari ini?
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5 font-medium">
                {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }} &bull; Sistem pemulihan personal yang memahami pola, bukan sekadar suasana hati.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <button @click="$dispatch('open-checkin')" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-nara-600 hover:bg-nara-700 text-white font-bold text-xs sm:text-sm shadow-md shadow-nara-600/20 transition transform active:scale-95">
                <x-icon name="plus" class="w-4 h-4 text-white" />
                <span>{{ $latestCheckin && $latestCheckin->date->isToday() ? 'Perbarui Check-in' : 'Catat Sinyal Hari Ini' }}</span>
            </button>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8" x-data="{ currentTab: 'overview' }">

        <!-- ========================================== -->
        <!-- MODULE 2: "WHAT CHANGED?" ENGINE NOTIFICATION BANNER -->
        <!-- ========================================== -->
        @if ($weeklyAnalysis['has_comparison'] && !empty($weeklyAnalysis['alerts']))
            <div class="bg-gradient-to-r from-[#21364A] via-[#2A4869] to-[#1E3A5F] rounded-3xl p-6 sm:p-7 text-white shadow-xl border border-white/15 relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>

                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-white/15 border border-white/25 flex items-center justify-center text-white flex-shrink-0">
                            <x-icon name="pattern" class="w-5 h-5 text-amber-300" />
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold uppercase tracking-widest text-nara-200">What Changed Engine</span>
                                <span class="text-[10px] bg-white/15 text-white px-2.5 py-0.5 rounded-full border border-white/20 font-mono font-bold">&Delta; &ge; 15%</span>
                            </div>
                            <h3 class="text-lg font-black text-white">Perubahan Signifikan Terdeteksi Minggu Ini</h3>
                        </div>
                    </div>
                    <span class="text-xs text-white/70 font-medium">Perbandingan 7 hari terakhir vs 7 hari sebelumnya</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach ($weeklyAnalysis['alerts'] as $alert)
                        <div class="bg-white/10 backdrop-blur-xs rounded-2xl p-4 border border-white/15 hover:bg-white/15 transition">
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <span class="font-bold text-sm text-white">{{ $alert['title'] }}</span>
                                <span class="text-xs px-2.5 py-0.5 rounded-full font-bold {{ $alert['is_positive'] ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-400/30' : 'bg-rose-500/20 text-rose-300 border border-rose-400/30' }}">
                                    {{ $alert['delta_percent'] > 0 ? '+' : '' }}{{ $alert['delta_percent'] }}%
                                </span>
                            </div>
                            <p class="text-xs text-white/80 leading-relaxed font-normal">{{ $alert['insight'] }}</p>
                            <div class="mt-3 flex items-center justify-between text-[11px] text-white/60 pt-2 border-t border-white/10 font-medium">
                                <span>Sebelumnya: <strong class="text-white">{{ $alert['previous'] }}</strong></span>
                                <span>Saat ini: <strong class="text-white">{{ $alert['current'] }}</strong></span>
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

            <!-- ONE SMALL THING CARD (Dominant Blue & White) -->
            <div class="lg:col-span-2 bg-gradient-to-br from-[#557FA8] via-[#4D769E] to-[#345577] rounded-3xl p-6 sm:p-7 text-white shadow-card relative overflow-hidden flex flex-col justify-between border border-white/20"
                 x-data="{ completed: {{ $microAction['is_completed'] ? 'true' : 'false' }}, loading: false }">
                <div class="absolute -right-8 -top-8 w-40 h-40 bg-white/10 rounded-full blur-xl pointer-events-none"></div>

                <div>
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-xl bg-white/20 flex items-center justify-center text-white">
                                <x-icon name="sparkle" class="w-4 h-4 text-amber-200" />
                            </div>
                            <span class="text-xs font-bold uppercase tracking-wider text-white/90">One Small Thing &bull; Aksi Mikro Hari Ini</span>
                        </div>
                        <span class="text-[11px] bg-white/20 text-white px-3 py-1 rounded-full border border-white/25 font-bold capitalize">
                            Fokus: {{ $microAction['category'] }}
                        </span>
                    </div>

                    <h4 class="text-xl sm:text-2xl font-black text-white mb-2 leading-snug" :class="completed ? 'line-through opacity-75' : ''">
                        "{{ $microAction['title'] }}"
                    </h4>

                    <p class="text-xs sm:text-sm text-white/90 leading-relaxed mb-6 font-normal">
                        @if ($lowestVector)
                            Disesuaikan secara otomatis dari sinyal terendahmu hari ini (<strong>{{ $lowestVector['details']['name'] }}</strong>: {{ $lowestVector['details']['score'] }} poin). Satu langkah kecil bergesekan rendah untuk memulihkan ritmemu.
                        @else
                            Satu langkah kecil bergesekan rendah untuk menjaga kestabilan harimu.
                        @endif
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-4 border-t border-white/20">
                    <span class="text-xs text-white/90 font-medium" x-text="completed ? '✨ Keren! Aksi mikro hari ini sudah selesai.' : 'Yuk selesaikan sebelum malam ini.'"></span>

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
                        <button type="submit" :disabled="loading" class="px-5 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 shadow-sm"
                                :class="completed ? 'bg-white/20 hover:bg-white/30 text-white' : 'bg-white text-[#263F59] hover:bg-nara-50 shadow-md'">
                            <x-icon name="check" class="w-4 h-4" />
                            <span x-text="completed ? 'Selesai (Klik Batal)' : 'Tandai Selesai'"></span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- RECOVERY QUICK CARD -->
            <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/80 shadow-card flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-xl bg-nara-100 text-nara-700 flex items-center justify-center font-bold">
                                <x-icon name="recovery" class="w-4 h-4 text-nara-700" />
                            </div>
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Recovery Lab</span>
                        </div>
                        <a href="{{ route('recovery.index') }}" class="text-xs font-bold text-nara-700 hover:underline">Lihat Semua &rarr;</a>
                    </div>

                    <h4 class="text-base font-bold text-slate-800 mb-1">Aktivitas Pemulihan Terbaikmu</h4>
                    <p class="text-xs text-slate-500 mb-4 font-medium">Berdasarkan ranking eksperimen pemulihanmu:</p>

                    <div class="space-y-2.5">
                        @forelse (array_slice($recoveryData['ranked_activities'], 0, 2) as $act)
                            <div class="p-3 rounded-2xl bg-nara-50/70 border border-nara-100 flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-xl bg-white text-nara-800 border border-nara-200 flex items-center justify-center text-xs font-black shadow-2xs">
                                        #{{ $loop->iteration }}
                                    </div>
                                    <div>
                                        <h5 class="text-xs font-bold text-slate-800">{{ $act['name'] }}</h5>
                                        <span class="text-[11px] text-slate-500">{{ $act['default_duration_min'] }} menit</span>
                                    </div>
                                </div>
                                <span class="text-xs font-black text-nara-800 bg-white px-2.5 py-1 rounded-lg border border-nara-200 shadow-2xs">
                                    +{{ $act['avg_energy_delta'] ?? 15 }} Energi
                                </span>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 py-3 text-center">Belum ada eksperimen pemulihan dicatat.</p>
                        @endforelse
                    </div>
                </div>

                <a href="{{ route('recovery.index') }}" class="mt-4 w-full py-2.5 rounded-xl bg-nara-50 hover:bg-nara-100 text-center text-xs font-bold text-nara-800 border border-nara-200/70 transition block">
                    Mulai Eksperimen Rehat 15 Menit &rarr;
                </a>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- MODULE 1: 4 LIFE SIGNAL VECTORS -->
        <!-- ========================================== -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-black text-slate-800 tracking-tight">4 Vektor Sinyal Hidup</h3>
                    <p class="text-xs text-slate-500 font-medium">Kondisi kesehatan holistik dari dimensi mental, fisik, sosial, dan beban hidup.</p>
                </div>
                @if ($latestCheckin)
                    <span class="text-xs px-3.5 py-1.5 rounded-full font-bold bg-white text-slate-700 border border-slate-200 shadow-xs">
                        Tag Hari Ini: <strong class="text-nara-700">{{ $latestCheckin->primary_tag }}</strong>
                    </span>
                @endif
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

                <!-- VECTOR 1: MIND -->
                <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-card hover:shadow-card-hover transition space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-2xl bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center">
                                <x-icon name="mind" class="w-5 h-5 text-amber-600" />
                            </div>
                            <div>
                                <h4 class="font-bold text-sm text-slate-800">Pikiran (Mind)</h4>
                                <span class="text-[10px] text-slate-400 uppercase font-bold">Mental Clarity</span>
                            </div>
                        </div>
                        <span class="text-2xl font-black text-amber-600">{{ $latestCheckin ? round($latestCheckin->mind_score) : 70 }}</span>
                    </div>

                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                        <div class="bg-amber-500 h-2 rounded-full transition-all duration-700" style="width: {{ $latestCheckin ? $latestCheckin->mind_score : 70 }}%"></div>
                    </div>

                    <div class="space-y-2 text-xs text-slate-600 pt-1">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Suasana Hati (Mood)</span>
                            <span class="font-bold text-slate-800">{{ $latestCheckin?->signal?->mood_level ?? 70 }}%</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Kemampuan Fokus</span>
                            <span class="font-bold text-slate-800">{{ $latestCheckin?->signal?->focus_level ?? 70 }}%</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Tingkat Stres</span>
                            <span class="font-bold {{ ($latestCheckin?->signal?->stress_level ?? 30) > 60 ? 'text-rose-600' : 'text-slate-800' }}">{{ $latestCheckin?->signal?->stress_level ?? 30 }}%</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Overthinking</span>
                            <span class="font-bold text-slate-800">{{ $latestCheckin?->signal?->overthinking_level ?? 30 }}%</span>
                        </div>
                    </div>
                </div>

                <!-- VECTOR 2: BODY -->
                <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-card hover:shadow-card-hover transition space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-2xl bg-teal-50 text-teal-600 border border-teal-200 flex items-center justify-center">
                                <x-icon name="body" class="w-5 h-5 text-teal-600" />
                            </div>
                            <div>
                                <h4 class="font-bold text-sm text-slate-800">Tubuh (Body)</h4>
                                <span class="text-[10px] text-slate-400 uppercase font-bold">Physical Vitality</span>
                            </div>
                        </div>
                        <span class="text-2xl font-black text-teal-600">{{ $latestCheckin ? round($latestCheckin->body_score) : 70 }}</span>
                    </div>

                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                        <div class="bg-teal-500 h-2 rounded-full transition-all duration-700" style="width: {{ $latestCheckin ? $latestCheckin->body_score : 70 }}%"></div>
                    </div>

                    <div class="space-y-2 text-xs text-slate-600 pt-1">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Durasi Tidur</span>
                            <span class="font-bold text-slate-800">{{ $latestCheckin?->signal?->sleep_hours ?? 7.0 }} Jam</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Kualitas Tidur</span>
                            <span class="font-bold text-slate-800">{{ $latestCheckin?->signal?->sleep_quality ?? 75 }}%</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Tingkat Energi</span>
                            <span class="font-bold text-slate-800">{{ $latestCheckin?->signal?->energy_level ?? 70 }}%</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Aktivitas Fisik</span>
                            <span class="font-bold text-slate-800">{{ $latestCheckin?->signal?->physical_activity_min ?? 20 }} Menit</span>
                        </div>
                    </div>
                </div>

                <!-- VECTOR 3: SOCIAL -->
                <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-card hover:shadow-card-hover transition space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-2xl bg-purple-50 text-purple-600 border border-purple-200 flex items-center justify-center">
                                <x-icon name="social" class="w-5 h-5 text-purple-600" />
                            </div>
                            <div>
                                <h4 class="font-bold text-sm text-slate-800">Sosial (Social)</h4>
                                <span class="text-[10px] text-slate-400 uppercase font-bold">Connection</span>
                            </div>
                        </div>
                        <span class="text-2xl font-black text-purple-600">{{ $latestCheckin ? round($latestCheckin->social_score) : 70 }}</span>
                    </div>

                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                        <div class="bg-purple-500 h-2 rounded-full transition-all duration-700" style="width: {{ $latestCheckin ? $latestCheckin->social_score : 70 }}%"></div>
                    </div>

                    <div class="space-y-2 text-xs text-slate-600 pt-1">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Kualitas Interaksi</span>
                            <span class="font-bold text-slate-800">{{ $latestCheckin?->signal?->social_interaction_score ?? 70 }}%</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Rasa Kesepian</span>
                            <span class="font-bold {{ ($latestCheckin?->signal?->loneliness_score ?? 20) > 50 ? 'text-rose-600' : 'text-slate-800' }}">{{ $latestCheckin?->signal?->loneliness_score ?? 20 }}%</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Gesekan Hubungan</span>
                            <span class="font-bold text-slate-800">{{ $latestCheckin?->signal?->relationship_friction_score ?? 10 }}%</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Lingkaran Support</span>
                            <span class="font-bold text-emerald-600">Terhubung</span>
                        </div>
                    </div>
                </div>

                <!-- VECTOR 4: LIFE -->
                <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-card hover:shadow-card-hover transition space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-2xl bg-rose-50 text-rose-600 border border-rose-200 flex items-center justify-center">
                                <x-icon name="life" class="w-5 h-5 text-rose-600" />
                            </div>
                            <div>
                                <h4 class="font-bold text-sm text-slate-800">Hidup (Life)</h4>
                                <span class="text-[10px] text-slate-400 uppercase font-bold">Context & Work</span>
                            </div>
                        </div>
                        <span class="text-2xl font-black text-rose-600">{{ $latestCheckin ? round($latestCheckin->life_score) : 70 }}</span>
                    </div>

                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                        <div class="bg-rose-500 h-2 rounded-full transition-all duration-700" style="width: {{ $latestCheckin ? $latestCheckin->life_score : 70 }}%"></div>
                    </div>

                    <div class="space-y-2 text-xs text-slate-600 pt-1">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Beban Tugas / Kerja</span>
                            <span class="font-bold {{ ($latestCheckin?->signal?->workload_score ?? 40) > 70 ? 'text-rose-600' : 'text-slate-800' }}">{{ $latestCheckin?->signal?->workload_score ?? 40 }}%</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Tekanan Finansial</span>
                            <span class="font-bold text-slate-800">{{ $latestCheckin?->signal?->financial_pressure_score ?? 30 }}%</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Progres Tujuan</span>
                            <span class="font-bold text-emerald-600">{{ $latestCheckin?->signal?->goal_progress_score ?? 65 }}%</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Status Beban</span>
                            <span class="font-bold text-slate-800">{{ ($latestCheckin?->signal?->workload_score ?? 40) > 75 ? 'Tinggi' : 'Terkendali' }}</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- ========================================== -->
        <!-- 14-DAY SIGNAL TRENDS CHART -->
        <!-- ========================================== -->
        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/80 shadow-card">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div>
                    <h3 class="text-lg font-black text-slate-800">Tren Fluktuasi Sinyal Hidup 14 Hari</h3>
                    <p class="text-xs text-slate-500 font-medium">Melihat korelasi antara tidur, beban kerja, dan energimu dari waktu ke waktu.</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1 rounded-full bg-amber-50 text-amber-800 border border-amber-200">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span> Mind
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1 rounded-full bg-teal-50 text-teal-800 border border-teal-200">
                        <span class="w-2 h-2 rounded-full bg-teal-500"></span> Body
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1 rounded-full bg-purple-50 text-purple-800 border border-purple-200">
                        <span class="w-2 h-2 rounded-full bg-purple-500"></span> Social
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1 rounded-full bg-rose-50 text-rose-800 border border-rose-200">
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
                            borderColor: '#E09A3E',
                            backgroundColor: 'rgba(224, 154, 62, 0.06)',
                            borderWidth: 2.5,
                            tension: 0.35,
                            pointRadius: 3,
                            pointHoverRadius: 6,
                        },
                        {
                            label: 'Tubuh (Body)',
                            data: chartData.body,
                            borderColor: '#0D9488',
                            backgroundColor: 'rgba(13, 148, 136, 0.06)',
                            borderWidth: 2.5,
                            tension: 0.35,
                            pointRadius: 3,
                            pointHoverRadius: 6,
                        },
                        {
                            label: 'Sosial (Social)',
                            data: chartData.social,
                            borderColor: '#8B5CF6',
                            backgroundColor: 'rgba(139, 92, 246, 0.06)',
                            borderWidth: 2.5,
                            tension: 0.35,
                            pointRadius: 3,
                            pointHoverRadius: 6,
                        },
                        {
                            label: 'Hidup (Life)',
                            data: chartData.life,
                            borderColor: '#F43F5E',
                            backgroundColor: 'rgba(244, 63, 94, 0.06)',
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

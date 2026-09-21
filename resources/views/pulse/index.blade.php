<x-app-layout>
    <x-slot name="header">
        <div>
            <span class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-nara-800 bg-nara-100 px-3 py-1 rounded-full border border-nara-200/60">
                <x-icon name="pulse" class="w-3.5 h-3.5 text-nara-700" />
                <span>Tren Komunitas Anonim</span>
            </span>
            <h2 class="font-black text-2xl sm:text-3xl text-slate-800 tracking-tight mt-1.5">
                Pulse: Solidaritas Kesejahteraan Anak Muda
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                Melihat tren beban dan kesejahteraan sebayamu secara agregat & anonim. Kamu tidak sendirian dalam perjuanganmu.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-2xl bg-white border border-slate-200 text-xs text-slate-700 font-bold shadow-xs">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Minggu ke-{{ $pulseData['week_number'] }}, {{ $pulseData['year'] }}</span>
            </span>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        <!-- Role Filter Tabs -->
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex bg-slate-100 p-1.5 rounded-2xl gap-1 text-xs font-bold">
                <a href="{{ route('pulse.index', ['role' => 'all']) }}" class="px-4 py-2 rounded-xl transition {{ $roleFilter === 'all' ? 'bg-nara-600 text-white shadow-xs font-black' : 'text-slate-600 hover:text-slate-900' }}">
                    Semua Komunitas
                </a>
                <a href="{{ route('pulse.index', ['role' => 'student']) }}" class="px-4 py-2 rounded-xl transition {{ $roleFilter === 'student' ? 'bg-nara-600 text-white shadow-xs font-black' : 'text-slate-600 hover:text-slate-900' }}">
                    Mahasiswa / Pelajar
                </a>
                <a href="{{ route('pulse.index', ['role' => 'fresh_grad']) }}" class="px-4 py-2 rounded-xl transition {{ $roleFilter === 'fresh_grad' ? 'bg-nara-600 text-white shadow-xs font-black' : 'text-slate-600 hover:text-slate-900' }}">
                    Fresh Graduate
                </a>
                <a href="{{ route('pulse.index', ['role' => 'young_worker']) }}" class="px-4 py-2 rounded-xl transition {{ $roleFilter === 'young_worker' ? 'bg-nara-600 text-white shadow-xs font-black' : 'text-slate-600 hover:text-slate-900' }}">
                    Pekerja Muda
                </a>
            </div>

            <div class="text-xs text-slate-500 font-medium">
                Sampel Agregat: <strong class="text-slate-800">{{ $pulseData['total_community_checkins'] }}+ check-in anonim</strong>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- SOLIDARITY HIGHLIGHT CARDS -->
        <!-- ========================================== -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach ($pulseData['solidarity_insights'] as $insight)
                <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-card hover:shadow-card-hover transition flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-3">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-nara-700 bg-nara-50 px-2.5 py-0.5 rounded-full border border-nara-100">
                                {{ $insight['tag'] }}
                            </span>
                            <span class="text-2xl font-black text-nara-700">{{ $insight['stat'] }}</span>
                        </div>
                        <h4 class="font-black text-sm sm:text-base text-slate-800 mb-2">{{ $insight['title'] }}</h4>
                        <p class="text-xs text-slate-500 leading-relaxed font-normal">{{ $insight['message'] }}</p>
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center gap-1.5 text-xs text-nara-800 font-bold">
                        <x-icon name="heart" class="w-3.5 h-3.5 text-nara-600" />
                        <span>Solidaritas: Kamu nggak sendiri</span>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- ========================================== -->
        <!-- DETAILED COMMUNITY STATS -->
        <!-- ========================================== -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Aggregates Progress Bars -->
            <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/80 shadow-card">
                <h3 class="text-base sm:text-lg font-black text-slate-800 mb-1">Rata-rata Distribusi Tantangan Minggu Ini</h3>
                <p class="text-xs text-slate-500 mb-6">Persentase pengguna yang melaporkan tantangan berikut:</p>

                <div class="space-y-5">
                    @foreach ($pulseData['aggregates'] as $agg)
                        <div>
                            <div class="flex justify-between text-xs font-bold text-slate-700 mb-1.5">
                                <span>{{ $agg->meta_json['label'] ?? ucwords(str_replace('_', ' ', $agg->metric_name)) }}</span>
                                <span class="font-black text-nara-700">{{ $agg->aggregate_value }}%</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                                <div class="bg-gradient-to-r from-nara-500 to-nara-700 h-2.5 rounded-full transition-all duration-700" style="width: {{ $agg->aggregate_value }}%"></div>
                            </div>
                            <div class="flex justify-between text-[11px] text-slate-400 mt-1 font-medium">
                                <span>{{ $agg->meta_json['top_challenge'] ?? '' }}</span>
                                <span>{{ $agg->sample_count }} respons</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Solidarity Reflection Note -->
            <div class="bg-gradient-to-br from-[#345577] via-[#436B92] to-[#557FA8] rounded-3xl p-6 sm:p-7 text-white shadow-card flex flex-col justify-between border border-white/20">
                <div>
                    <span class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-widest text-nara-100 bg-white/15 px-3 py-1 rounded-full border border-white/20 mb-3">
                        <x-icon name="sparkle" class="w-3.5 h-3.5 text-white" />
                        <span>Pesan Solidaritas NARA</span>
                    </span>
                    <h3 class="text-xl sm:text-2xl font-black text-white mb-3">Normalisasi Rasa Lelah & Rehat</h3>
                    <p class="text-xs sm:text-sm text-white/90 leading-relaxed mb-3 font-normal">
                        Di era serba cepat ini, media sosial seringkali hanya menampilkan pencapaian dan puncak sukses orang lain. Data Pulse menunjukkan kenyataan sebenarnya: sebagian besar teman-teman seusiamu juga merasakan kelelahan, overthinking karir, dan tekanan tugas yang serupa.
                    </p>
                    <p class="text-xs sm:text-sm text-white/80 leading-relaxed font-normal">
                        Mengakui bahwa kamu sedang lelah bukan tanda kegagalan. Memberi jeda bagi dirimu sendiri adalah bentuk tanggung jawab tertinggi terhadap masa depanmu.
                    </p>
                </div>

                <div class="mt-6 pt-4 border-t border-white/20 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <a href="{{ route('circle.index') }}" class="text-xs font-bold text-white/90 hover:text-white flex items-center gap-1.5">
                        <x-icon name="circle" class="w-4 h-4 text-white" />
                        <span>Hubungi Lingkaran Support &rarr;</span>
                    </a>
                    <a href="{{ route('reflection.index') }}" class="px-5 py-2.5 rounded-xl bg-white text-[#263F59] hover:bg-[#F8F6F0] text-xs font-bold shadow-md transition text-center">
                        Mulai Refleksi Mandiri
                    </a>
                </div>
            </div>

        </div>

        <!-- Privacy Box -->
        <div class="bg-nara-50/70 rounded-2xl p-4 sm:p-5 border border-nara-200/60 flex items-center gap-3 text-xs text-nara-900">
            <div class="w-8 h-8 rounded-xl bg-nara-100 flex items-center justify-center flex-shrink-0">
                <x-icon name="privacy" class="w-4 h-4 text-nara-700" />
            </div>
            <span>
                <strong>Jaminan Privasi Penuh:</strong> Data Pulse dihitung dari check-in agregat tanpa nama, email, atau catatan personal. Kamu dapat menonaktifkan kontribusi anonim kapan saja di <a href="{{ route('privacy.index') }}" class="underline font-bold text-nara-800">Pusat Privasi</a>.
            </span>
        </div>

    </div>
</x-app-layout>

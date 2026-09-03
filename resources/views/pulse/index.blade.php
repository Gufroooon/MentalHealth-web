{{--
    Dokumentasi file: View Blade.

    Menjelaskan tanggung jawab file resources/views/pulse/index.blade.php serta hubungan data atau UI-nya dengan bagian aplikasi lain.
--}}
<x-app-layout>
    <x-slot name="header">
        <div>
            <span class="text-xs font-semibold uppercase tracking-wider text-indigo-800 bg-indigo-100 px-3 py-1 rounded-full">
                🌐 Tren Komunitas Anonim
            </span>
            <h2 class="font-extrabold text-2xl text-slate-800 tracking-tight mt-1">
                Pulse: Solidaritas Kesejahteraan Anak Muda
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                Melihat tren beban dan kesejahteraan sebayamu secara agregat & anonim. Kamu tidak sendirian dalam perjuanganmu.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-2xl bg-white border border-slate-200 text-xs text-slate-600 font-semibold shadow-sm">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Minggu ke-{{ $pulseData['week_number'] }}, {{ $pulseData['year'] }}</span>
            </span>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        <!-- Role Filter Tabs -->
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex bg-slate-100 p-1.5 rounded-2xl gap-1 text-xs font-bold">
                <a href="{{ route('pulse.index', ['role' => 'all']) }}" class="px-4 py-2 rounded-xl transition {{ $roleFilter === 'all' ? 'bg-white text-indigo-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                    👥 Semua Komunitas
                </a>
                <a href="{{ route('pulse.index', ['role' => 'student']) }}" class="px-4 py-2 rounded-xl transition {{ $roleFilter === 'student' ? 'bg-white text-indigo-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                    🎓 Mahasiswa / Pelajar
                </a>
                <a href="{{ route('pulse.index', ['role' => 'fresh_grad']) }}" class="px-4 py-2 rounded-xl transition {{ $roleFilter === 'fresh_grad' ? 'bg-white text-indigo-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                    💼 Fresh Graduate
                </a>
                <a href="{{ route('pulse.index', ['role' => 'young_worker']) }}" class="px-4 py-2 rounded-xl transition {{ $roleFilter === 'young_worker' ? 'bg-white text-indigo-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                    🏢 Pekerja Muda
                </a>
            </div>

            <div class="text-xs text-slate-400">
                Sampel Agregat: <strong class="text-slate-700">{{ $pulseData['total_community_checkins'] }}+ check-in anonim</strong>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- SOLIDARITY HIGHLIGHT CARDS -->
        <!-- ========================================== -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach ($pulseData['solidarity_insights'] as $insight)
                <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-3">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-full border border-indigo-100">
                                {{ $insight['tag'] }}
                            </span>
                            <span class="text-xl font-black text-indigo-600">{{ $insight['stat'] }}</span>
                        </div>
                        <h4 class="font-bold text-sm text-slate-800 mb-2">{{ $insight['title'] }}</h4>
                        <p class="text-xs text-slate-500 leading-relaxed">{{ $insight['message'] }}</p>
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center gap-1.5 text-[11px] text-indigo-800 font-semibold">
                        <span>🤝 Solidaritas: Kamu nggak sendiri</span>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- ========================================== -->
        <!-- DETAILED COMMUNITY STATS -->
        <!-- ========================================== -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Aggregates Progress Bars -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm">
                <h3 class="text-base font-bold text-slate-800 mb-1">Rata-rata Distribusi Tantangan Minggu Ini</h3>
                <p class="text-xs text-slate-500 mb-6">Persentase pengguna yang melaporkan tantangan berikut:</p>

                <div class="space-y-5">
                    @foreach ($pulseData['aggregates'] as $agg)
                        <div>
                            <div class="flex justify-between text-xs font-semibold text-slate-700 mb-1.5">
                                <span>{{ $agg->meta_json['label'] ?? ucwords(str_replace('_', ' ', $agg->metric_name)) }}</span>
                                <span class="font-black text-indigo-700">{{ $agg->aggregate_value }}%</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 h-2.5 rounded-full transition-all duration-700" style="width: {{ $agg->aggregate_value }}%"></div>
                            </div>
                            <div class="flex justify-between text-[11px] text-slate-400 mt-1">
                                <span>{{ $agg->meta_json['top_challenge'] ?? '' }}</span>
                                <span>{{ $agg->sample_count }} respons</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Solidarity Reflection Note -->
            <div class="bg-gradient-to-br from-slate-900 to-indigo-950 rounded-3xl p-6 text-white shadow-xl flex flex-col justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-widest text-indigo-300 block mb-2">Pesan Solidaritas NARA</span>
                    <h3 class="text-xl font-bold text-white mb-3">Normalisasi Rasa Lelah & Rehat</h3>
                    <p class="text-xs text-indigo-100 leading-relaxed mb-4">
                        Di era serba cepat ini, media sosial seringkali hanya menampilkan pencapaian dan puncak sukses orang lain. Data Pulse menunjukkan kenyataan sebenarnya: sebagian besar teman-teman seusiamu juga merasakan kelelahan, overthinking karir, dan tekanan tugas yang sama.
                    </p>
                    <p class="text-xs text-indigo-200 leading-relaxed">
                        Mengakui bahwa kamu sedang lelah bukan tanda kegagalan. Memberi jeda bagi dirimu sendiri adalah bentuk tanggung jawab tertinggi terhadap masa depanmu.
                    </p>
                </div>

                <div class="mt-6 pt-4 border-t border-indigo-800/60 flex items-center justify-between">
                    <a href="{{ route('circle.index') }}" class="text-xs font-bold text-indigo-300 hover:text-white flex items-center gap-1">
                        <span>Hubungi Lingkaran Support Terdekat &rarr;</span>
                    </a>
                    <a href="{{ route('reflection.index') }}" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold transition">
                        Mulai Refleksi Mandiri
                    </a>
                </div>
            </div>

        </div>

        <!-- Privacy Box -->
        <div class="bg-indigo-50/70 rounded-2xl p-4 border border-indigo-200/60 flex items-center gap-3 text-xs text-indigo-900">
            <span class="text-lg">🔒</span>
            <span>
                <strong>Jaminan Privasi Penuh:</strong> Data Pulse dihitung dari check-in agregat tanpa nama, email, atau catatan personal. Kamu dapat menonaktifkan kontribusi anonim kapan saja di <a href="{{ route('privacy.index') }}" class="underline font-bold">Pusat Privasi</a>.
            </span>
        </div>

    </div>
</x-app-layout>

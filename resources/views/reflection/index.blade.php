{{--
    Dokumentasi file: View Blade.

    Menjelaskan tanggung jawab file resources/views/reflection/index.blade.php serta hubungan data atau UI-nya dengan bagian aplikasi lain.
--}}
<x-app-layout>
    <x-slot name="header">
        <div>
            <span class="text-xs font-semibold uppercase tracking-wider text-emerald-800 bg-emerald-100 px-3 py-1 rounded-full">
                🧘 Ruang Refleksi Tenang
            </span>
            <h2 class="font-extrabold text-2xl text-slate-800 tracking-tight mt-1">
                Asisten Refleksi Berbasis Pengetahuan
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                Ruang aman untuk mengurai keruwetan isi kepala dengan panduan empati terstruktur — 100% deterministik tanpa data keluar ke API AI.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-xs px-3 py-1.5 rounded-2xl bg-white border border-slate-200 text-slate-600 font-semibold shadow-sm">
                🔒 Catatan Terenkripsi & Privat
            </span>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8" x-data="{
        category: '{{ $preferredCategory ?? 'all' }}',
        activeRuleTitle: '{{ $matchedRule?->title ?? '' }}',
        activePrompt: @js($matchedRule?->reflection_prompt ?? ''),
        activeQuestion: @js($matchedRule?->guided_question ?? ''),
        activeActionTitle: @js($matchedRule?->action_title ?? ''),
        activeActionDesc: @js($matchedRule?->action_suggestion ?? ''),
        ruleId: '{{ $matchedRule?->id ?? '' }}',
        userResponse: '',
        moodAfter: 75
    }">

        <!-- Category Selector Pills -->
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('reflection.index', ['category' => 'all']) }}" class="px-4 py-2 rounded-2xl text-xs font-bold transition {{ $preferredCategory === 'all' ? 'bg-emerald-800 text-white shadow-md' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50' }}">
                🌿 Rekomendasi Sinyalmu
            </a>
            <a href="{{ route('reflection.index', ['category' => 'mind']) }}" class="px-4 py-2 rounded-2xl text-xs font-bold transition {{ $preferredCategory === 'mind' ? 'bg-emerald-800 text-white shadow-md' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50' }}">
                🧠 Pikiran & Overthinking
            </a>
            <a href="{{ route('reflection.index', ['category' => 'body']) }}" class="px-4 py-2 rounded-2xl text-xs font-bold transition {{ $preferredCategory === 'body' ? 'bg-emerald-800 text-white shadow-md' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50' }}">
                ⚡ Kelelahan Fisik & Tidur
            </a>
            <a href="{{ route('reflection.index', ['category' => 'social']) }}" class="px-4 py-2 rounded-2xl text-xs font-bold transition {{ $preferredCategory === 'social' ? 'bg-emerald-800 text-white shadow-md' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50' }}">
                👥 Hubungan & Rasa Sepi
            </a>
            <a href="{{ route('reflection.index', ['category' => 'life']) }}" class="px-4 py-2 rounded-2xl text-xs font-bold transition {{ $preferredCategory === 'life' ? 'bg-emerald-800 text-white shadow-md' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50' }}">
                🎯 Beban Kerja & Finansial
            </a>
            <a href="{{ route('reflection.index', ['category' => 'combination']) }}" class="px-4 py-2 rounded-2xl text-xs font-bold transition {{ $preferredCategory === 'combination' ? 'bg-emerald-800 text-white shadow-md' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50' }}">
                🔗 Pola Kombinasi
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- REFLECTION COACH (LEFT 2 COLS) -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Coach Reflection Card -->
                <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-5">
                    
                    <!-- Coach Greeting / Empathy Bubble -->
                    <div class="flex items-start gap-3.5">
                        <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-emerald-800 to-teal-600 flex items-center justify-center text-white text-xl flex-shrink-0 shadow-sm">
                            🌿
                        </div>
                        <div class="bg-slate-50 rounded-2xl rounded-tl-none p-4 border border-slate-100 flex-1">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-xs font-bold text-emerald-800">NARA Coach</span>
                                <span class="text-[10px] bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-full font-semibold">Knowledge Base Match</span>
                            </div>
                            <h3 class="text-sm font-bold text-slate-800 mb-2" x-text="activeRuleTitle"></h3>
                            <p class="text-xs text-slate-600 leading-relaxed" x-text="activePrompt"></p>
                        </div>
                    </div>

                    <!-- Guided Question Box -->
                    <div class="bg-amber-50/70 rounded-2xl p-5 border border-amber-200/60">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-amber-800 block mb-1">Pertanyaan Refleksi Terpandu:</span>
                        <h4 class="text-base font-bold text-amber-950 leading-snug" x-text="activeQuestion"></h4>
                    </div>

                    <!-- Micro-Action Suggestion -->
                    <template x-if="activeActionTitle">
                        <div class="bg-emerald-50/70 rounded-2xl p-4 border border-emerald-200/60 flex items-start gap-3">
                            <span class="text-lg">✨</span>
                            <div class="text-xs text-emerald-950">
                                <strong class="block mb-0.5" x-text="'Langkah Kecil: ' + activeActionTitle"></strong>
                                <span x-text="activeActionDesc"></span>
                            </div>
                        </div>
                    </template>

                    <!-- User Journal Form -->
                    <form action="{{ route('reflection.store') }}" method="POST" class="space-y-4 pt-2">
                        @csrf
                        <input type="hidden" name="rule_id" :value="ruleId">
                        <input type="hidden" name="prompt_topic" :value="activeRuleTitle">
                        <input type="hidden" name="prompt_snapshot" :value="activePrompt">
                        <input type="hidden" name="question_snapshot" :value="activeQuestion">

                        <div>
                            <div class="flex justify-between items-center mb-1.5">
                                <label class="text-xs font-bold text-slate-700">Tuliskan isi pikiranmu dengan bebas di sini:</label>
                                <span class="text-[11px] text-slate-400">Tidak ada jawaban benar/salah</span>
                            </div>
                            <textarea name="user_response" x-model="userResponse" rows="4" required placeholder="Tuliskan apa saja yang kamu rasakan tanpa perlu mengoreksi gaya bahasamu..." class="w-full rounded-2xl border-slate-200 text-xs focus:border-emerald-500 focus:ring-emerald-500 placeholder:text-slate-400"></textarea>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-xs">
                            <div class="flex-1">
                                <div class="flex justify-between text-xs mb-1">
                                    <span class="font-semibold text-slate-700">Gimana perasaanmu setelah menuliskannya?</span>
                                    <span class="font-bold text-emerald-700" x-text="moodAfter + '/100'"></span>
                                </div>
                                <input type="range" name="mood_after" min="0" max="100" x-model="moodAfter" class="w-full accent-emerald-600">
                            </div>
                            <button type="submit" :disabled="!userResponse.trim()" class="px-6 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 disabled:opacity-50 text-white font-bold text-xs shadow-md transition flex items-center justify-center gap-2 flex-shrink-0">
                                <span>Simpan Catatan Refleksi</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </button>
                        </div>
                    </form>

                </div>

            </div>

            <!-- PREVIOUS JOURNALS HISTORY (RIGHT COL) -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-bold text-slate-800">Riwayat Jurnal Pribadi</h3>
                    <span class="text-xs text-slate-400">{{ $journalsHistory->count() }} Catatan</span>
                </div>

                <div class="space-y-3 max-h-[70vh] overflow-y-auto pr-1">
                    @forelse ($journalsHistory as $journal)
                        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm space-y-2 text-xs">
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-bold text-slate-800 truncate">{{ $journal->prompt_topic }}</span>
                                <span class="text-[10px] text-slate-400 flex-shrink-0">{{ $journal->created_at->translatedFormat('d M, H:i') }}</span>
                            </div>
                            
                            @if ($journal->question_snapshot)
                                <p class="text-[11px] text-amber-900 bg-amber-50/60 p-2 rounded-xl border border-amber-100">
                                    Q: {{ $journal->question_snapshot }}
                                </p>
                            @endif

                            <p class="text-slate-600 leading-relaxed whitespace-pre-line bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                                "{{ $journal->user_response }}"
                            </p>

                            @if ($journal->mood_after)
                                <div class="flex justify-between items-center text-[10px] text-slate-400 pt-1">
                                    <span>Kondisi Batin Setelahnya:</span>
                                    <span class="font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md">{{ $journal->mood_after }}/100</span>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="bg-white rounded-2xl p-6 border border-slate-200 text-center text-xs text-slate-400">
                            Belum ada catatan refleksi yang tersimpan. Coba curahkan isi hatimu di kolom sebelah kiri.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>
</x-app-layout>

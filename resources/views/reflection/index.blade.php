<x-app-layout>
    <x-slot name="header">
        <div>
            <span class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-nara-800 bg-nara-100 px-3 py-1 rounded-full border border-nara-200/60">
                <x-icon name="reflection" class="w-3.5 h-3.5 text-nara-700" />
                <span>Ruang Refleksi Tenang</span>
            </span>
            <h2 class="font-black text-2xl sm:text-3xl text-slate-800 tracking-tight mt-1.5">
                Asisten Refleksi Berbasis Pengetahuan
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                Ruang aman untuk mengurai keruwetan isi kepala dengan panduan empati terstruktur &mdash; 100% deterministik tanpa data keluar ke API AI.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 text-xs px-3.5 py-1.5 rounded-2xl bg-white border border-slate-200 text-slate-700 font-bold shadow-xs">
                <x-icon name="privacy" class="w-3.5 h-3.5 text-emerald-600" />
                <span>Catatan Privat</span>
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
            <a href="{{ route('reflection.index', ['category' => 'all']) }}" class="px-4 py-2.5 rounded-2xl text-xs font-bold transition {{ $preferredCategory === 'all' ? 'bg-nara-600 text-white shadow-md shadow-nara-600/20' : 'bg-white text-slate-700 border border-slate-200 hover:bg-nara-50' }}">
                Rekomendasi Sinyalmu
            </a>
            <a href="{{ route('reflection.index', ['category' => 'mind']) }}" class="px-4 py-2.5 rounded-2xl text-xs font-bold transition {{ $preferredCategory === 'mind' ? 'bg-nara-600 text-white shadow-md shadow-nara-600/20' : 'bg-white text-slate-700 border border-slate-200 hover:bg-nara-50' }}">
                Pikiran & Overthinking
            </a>
            <a href="{{ route('reflection.index', ['category' => 'body']) }}" class="px-4 py-2.5 rounded-2xl text-xs font-bold transition {{ $preferredCategory === 'body' ? 'bg-nara-600 text-white shadow-md shadow-nara-600/20' : 'bg-white text-slate-700 border border-slate-200 hover:bg-nara-50' }}">
                Kelelahan Fisik & Tidur
            </a>
            <a href="{{ route('reflection.index', ['category' => 'social']) }}" class="px-4 py-2.5 rounded-2xl text-xs font-bold transition {{ $preferredCategory === 'social' ? 'bg-nara-600 text-white shadow-md shadow-nara-600/20' : 'bg-white text-slate-700 border border-slate-200 hover:bg-nara-50' }}">
                Hubungan & Rasa Sepi
            </a>
            <a href="{{ route('reflection.index', ['category' => 'life']) }}" class="px-4 py-2.5 rounded-2xl text-xs font-bold transition {{ $preferredCategory === 'life' ? 'bg-nara-600 text-white shadow-md shadow-nara-600/20' : 'bg-white text-slate-700 border border-slate-200 hover:bg-nara-50' }}">
                Beban Kerja & Finansial
            </a>
            <a href="{{ route('reflection.index', ['category' => 'combination']) }}" class="px-4 py-2.5 rounded-2xl text-xs font-bold transition {{ $preferredCategory === 'combination' ? 'bg-nara-600 text-white shadow-md shadow-nara-600/20' : 'bg-white text-slate-700 border border-slate-200 hover:bg-nara-50' }}">
                Pola Kombinasi
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">

            <!-- REFLECTION COACH (LEFT 2 COLS) -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Coach Reflection Card -->
                <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/80 shadow-card space-y-6">

                    <!-- Coach Greeting / Empathy Bubble -->
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-[#557FA8] to-[#436B92] flex items-center justify-center text-white font-bold flex-shrink-0 shadow-sm">
                            <x-icon name="reflection" class="w-6 h-6 text-white" />
                        </div>
                        <div class="bg-[#F8F6F0] rounded-3xl rounded-tl-none p-5 border border-slate-200/60 flex-1 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-black text-nara-800 uppercase tracking-wide">NARA Reflection Companion</span>
                                <span class="text-[10px] bg-nara-100 text-nara-800 px-2.5 py-0.5 rounded-full font-bold">Knowledge Match</span>
                            </div>
                            <h3 class="text-base font-black text-slate-800" x-text="activeRuleTitle"></h3>
                            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed" x-text="activePrompt"></p>
                        </div>
                    </div>

                    <!-- Guided Question Box -->
                    <div class="bg-amber-50/80 rounded-2xl p-5 border border-amber-200/70 space-y-1.5">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-amber-800 block">Pertanyaan Refleksi Terpandu:</span>
                        <h4 class="text-base sm:text-lg font-black text-amber-950 leading-snug" x-text="activeQuestion"></h4>
                    </div>

                    <!-- Micro-Action Suggestion -->
                    <template x-if="activeActionTitle">
                        <div class="bg-nara-50/80 rounded-2xl p-4 border border-nara-200/60 flex items-start gap-3">
                            <div class="w-8 h-8 rounded-xl bg-nara-100 text-nara-700 flex items-center justify-center flex-shrink-0">
                                <x-icon name="sparkle" class="w-4 h-4" />
                            </div>
                            <div class="text-xs text-nara-950">
                                <strong class="block mb-0.5 font-bold" x-text="'Langkah Kecil: ' + activeActionTitle"></strong>
                                <span x-text="activeActionDesc" class="leading-relaxed"></span>
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
                                <span class="text-[11px] text-slate-400 font-medium">Tidak ada jawaban benar/salah</span>
                            </div>
                            <textarea name="user_response" x-model="userResponse" rows="5" required placeholder="Tuliskan apa saja yang kamu rasakan tanpa perlu mengoreksi gaya bahasamu..." class="w-full rounded-2xl border-slate-200 text-xs sm:text-sm focus:border-nara-500 focus:ring-nara-500 placeholder:text-slate-400 p-3.5"></textarea>
                        </div>

                        <div class="p-4 sm:p-5 rounded-2xl bg-[#F8F6F0] border border-slate-200/60 flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-xs">
                            <div class="flex-1">
                                <div class="flex justify-between text-xs mb-1">
                                    <span class="font-bold text-slate-700">Gimana perasaanmu setelah menuliskannya?</span>
                                    <span class="font-black text-nara-700" x-text="moodAfter + '/100'"></span>
                                </div>
                                <input type="range" name="mood_after" min="0" max="100" x-model="moodAfter" class="w-full accent-nara-600">
                            </div>
                            <button type="submit" :disabled="!userResponse.trim()" class="px-6 py-3 rounded-2xl bg-nara-600 hover:bg-nara-700 disabled:opacity-50 text-white font-bold text-xs shadow-md shadow-nara-600/20 transition flex items-center justify-center gap-2 flex-shrink-0">
                                <x-icon name="check" class="w-4 h-4" />
                                <span>Simpan Catatan Refleksi</span>
                            </button>
                        </div>
                    </form>

                </div>

            </div>

            <!-- PREVIOUS JOURNALS HISTORY (RIGHT COL) -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-black text-slate-800">Riwayat Jurnal Pribadi</h3>
                    <span class="text-xs text-slate-400 font-bold">{{ $journalsHistory->count() }} Catatan</span>
                </div>

                <div class="space-y-3 max-h-[70vh] overflow-y-auto pr-1">
                    @forelse ($journalsHistory as $journal)
                        <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-card space-y-2.5 text-xs">
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-black text-slate-800 truncate">{{ $journal->prompt_topic }}</span>
                                <span class="text-[10px] text-slate-400 font-bold flex-shrink-0">{{ $journal->created_at->translatedFormat('d M, H:i') }}</span>
                            </div>

                            @if ($journal->question_snapshot)
                                <p class="text-[11px] text-amber-900 bg-amber-50/70 p-2.5 rounded-xl border border-amber-100 font-medium">
                                    Q: {{ $journal->question_snapshot }}
                                </p>
                            @endif

                            <p class="text-slate-600 leading-relaxed whitespace-pre-line bg-[#F8F6F0] p-3 rounded-xl border border-slate-200/60 font-normal">
                                "{{ $journal->user_response }}"
                            </p>

                            @if ($journal->mood_after)
                                <div class="flex justify-between items-center text-[10px] text-slate-400 pt-1 border-t border-slate-100">
                                    <span>Kondisi Batin:</span>
                                    <span class="font-black text-nara-700 bg-nara-50 px-2 py-0.5 rounded-md">{{ $journal->mood_after }}/100</span>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="bg-white rounded-3xl p-8 border border-slate-200 text-center text-xs text-slate-400">
                            Belum ada catatan refleksi yang tersimpan. Coba curahkan isi hatimu di kolom sebelah kiri.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>
</x-app-layout>

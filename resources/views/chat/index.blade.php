{{--
    Dokumentasi file: View Blade.

    Menjelaskan tanggung jawab file resources/views/chat/index.blade.php serta hubungan data atau UI-nya dengan bagian aplikasi lain.
--}}
<x-app-layout>
    <x-slot name="header">
        <div>
            <span class="text-xs font-semibold uppercase tracking-wider text-emerald-800 bg-emerald-100 px-3 py-1 rounded-full">
                💬 Teman Ngobrol NARA
            </span>
            <h2 class="font-extrabold text-2xl text-slate-800 tracking-tight mt-1">
                Chatbot Pendamping Kesejahteraan
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                Obrolan interaktif 24/7 untuk curhat, konsultasi rehat, dan mengatasi overthinking tanpa khawatir data bocor.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <form action="{{ route('chat.clear') }}" method="POST" onsubmit="return confirm('Bersihkan riwayat percakapan chat ini?')">
                @csrf
                <button type="submit" class="text-xs font-semibold px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 transition">
                    🧹 Bersihkan Chat
                </button>
            </form>
        </div>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xl overflow-hidden flex flex-col h-[75vh]"
             x-data="{
                messages: @js($messages->map(fn($m) => [
                    'id' => $m->id,
                    'sender' => $m->sender,
                    'message' => $m->message,
                    'quick_replies' => $m->quick_replies_json ?? [],
                    'time' => $m->created_at->format('H:i')
                ])),
                inputText: '',
                isTyping: false,

                scrollToBottom() {
                    this.$nextTick(() => {
                        const container = this.$refs.chatContainer;
                        if (container) {
                            container.scrollTop = container.scrollHeight;
                        }
                    });
                },

                sendMessage(textToSend = null) {
                    const text = textToSend || this.inputText.trim();
                    if (!text) return;

                    // Push user message immediately
                    this.messages.push({
                        id: Date.now(),
                        sender: 'user',
                        message: text,
                        quick_replies: [],
                        time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
                    });

                    this.inputText = '';
                    this.isTyping = true;
                    this.scrollToBottom();

                    // Send to backend via AJAX
                    fetch('{{ route('chat.send') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ message: text })
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.isTyping = false;
                        if (data.success) {
                            this.messages.push(data.nara_message);
                            this.scrollToBottom();
                        }
                    })
                    .catch(err => {
                        this.isTyping = false;
                        this.messages.push({
                            id: Date.now(),
                            sender: 'nara',
                            message: 'Maaf, terjadi gangguan koneksi lokal. Coba ketik lagi ya!',
                            quick_replies: [],
                            time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
                        });
                        this.scrollToBottom();
                    });
                },

                actionMap: {
                    'catat rasa syukur di jurnal': '{{ route('reflection.index') }}',
                    'mau tulis jurnal refleksi': '{{ route('reflection.index') }}',
                    'buka jurnal refleksi nara': '{{ route('reflection.index') }}',
                    'buka recovery lab': '{{ route('recovery.index') }}',
                    'catat check-in hari ini': '{{ route('dashboard') }}',
                    'catat check-in dulu': '{{ route('dashboard') }}',
                    'mulai check-in hari ini': '{{ route('dashboard') }}',
                    'lihat sinyal hidupku': '{{ route('dashboard') }}',
                    'buka pusat privasi': '{{ route('privacy.index') }}',
                    'ekspor data aku': '{{ route('privacy.index') }}',
                    'coba what-if simulator': '{{ route('pattern.index') }}',
                    'lihat tren 14 hari terakhir': '{{ route('pattern.index') }}',
                    'buka lingkaran support': '{{ route('circle.index') }}',
                    'kirim sinyal ke sahabat': '{{ route('circle.index') }}',
                    'hubungi hotline sekarang': 'tel:119',
                },

                handleQuickReply(qr) {
                    const clean = qr.toLowerCase().trim();
                    if (this.actionMap[clean]) {
                        window.location.href = this.actionMap[clean];
                        return;
                    }
                    if (clean.includes('jurnal') || clean.includes('rasa syukur')) {
                        window.location.href = '{{ route('reflection.index') }}';
                        return;
                    }
                    if (clean.includes('recovery lab')) {
                        window.location.href = '{{ route('recovery.index') }}';
                        return;
                    }
                    if (clean.includes('pusat privasi') || clean.includes('ekspor data')) {
                        window.location.href = '{{ route('privacy.index') }}';
                        return;
                    }
                    if (clean.includes('lingkaran support') || clean.includes('sinyal ke sahabat')) {
                        window.location.href = '{{ route('circle.index') }}';
                        return;
                    }
                    if (clean.includes('what-if') || clean.includes('tren 14 hari')) {
                        window.location.href = '{{ route('pattern.index') }}';
                        return;
                    }
                    if (clean.includes('check-in') || clean.includes('sinyal hidupku')) {
                        window.location.href = '{{ route('dashboard') }}';
                        return;
                    }
                    if (clean.includes('hotline')) {
                        window.location.href = 'tel:119';
                        return;
                    }

                    this.sendMessage(qr);
                },

                isActionLink(qr) {
                    const clean = qr.toLowerCase().trim();
                    return !!(this.actionMap[clean] || 
                        clean.includes('jurnal') || 
                        clean.includes('recovery lab') || 
                        clean.includes('pusat privasi') || 
                        clean.includes('lingkaran support') || 
                        clean.includes('what-if') || 
                        clean.includes('check-in') || 
                        clean.includes('hotline'));
                },

                init() {
                    this.scrollToBottom();
                }
             }">

            <!-- Chat Header -->
            <div class="px-6 py-4 bg-gradient-to-r from-emerald-800 to-teal-700 text-white flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-white/20 flex items-center justify-center text-xl font-bold">
                        🌿
                    </div>
                    <div>
                        <h3 class="font-bold text-sm text-white flex items-center gap-2">
                            <span>NARA Assistant</span>
                            <span class="inline-block w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        </h3>
                        <span class="text-[11px] text-emerald-100">Teman Diskusi Kesejahteraan Hidup (Deterministik)</span>
                    </div>
                </div>
                <div class="text-xs bg-white/10 px-3 py-1 rounded-full border border-white/20 text-emerald-100 hidden sm:block">
                    🔒 100% Chat Privat
                </div>
            </div>

            <!-- Chat Message Stream -->
            <div x-ref="chatContainer" class="flex-1 p-6 overflow-y-auto space-y-4 bg-slate-50/70">
                <template x-for="(msg, index) in messages" :key="msg.id || index">
                    <div>
                        <!-- Bot Message -->
                        <template x-if="msg.sender === 'nara'">
                            <div class="flex items-start gap-3 max-w-[85%] sm:max-w-[75%]">
                                <div class="w-8 h-8 rounded-xl bg-emerald-700 text-white flex items-center justify-center text-sm font-bold flex-shrink-0 mt-1 shadow-sm">
                                    🌿
                                </div>
                                <div class="space-y-2">
                                    <div class="bg-white p-4 rounded-2xl rounded-tl-sm border border-slate-200/80 shadow-xs text-xs text-slate-800 leading-relaxed whitespace-pre-line">
                                        <p x-text="msg.message"></p>
                                        <span class="block text-[10px] text-slate-400 text-right mt-1" x-text="msg.time"></span>
                                    </div>

                                    <!-- Quick Reply Chips -->
                                    <template x-if="msg.quick_replies && msg.quick_replies.length > 0">
                                        <div class="flex flex-wrap gap-1.5 pt-1">
                                            <template x-for="qr in msg.quick_replies" :key="qr">
                                                <button type="button" 
                                                        @click="handleQuickReply(qr)" 
                                                        :class="isActionLink(qr) ? 'bg-teal-50 hover:bg-teal-100 text-teal-800 border-teal-300 font-bold' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border-emerald-200'"
                                                        class="text-[11px] font-semibold px-3 py-1.5 rounded-xl border transition shadow-xs flex items-center gap-1.5 cursor-pointer">
                                                    <span x-show="isActionLink(qr)" class="text-[10px]">🚀</span>
                                                    <span x-text="qr"></span>
                                                    <span x-show="!isActionLink(qr)">&rarr;</span>
                                                    <span x-show="isActionLink(qr)" class="text-[9px] bg-teal-200/60 text-teal-900 px-1 rounded">Buka Menu</span>
                                                </button>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <!-- User Message -->
                        <template x-if="msg.sender === 'user'">
                            <div class="flex items-end justify-end gap-2">
                                <div class="bg-emerald-700 text-white p-4 rounded-2xl rounded-tr-sm shadow-sm max-w-[85%] sm:max-w-[75%] text-xs leading-relaxed">
                                    <p x-text="msg.message"></p>
                                    <span class="block text-[10px] text-emerald-200 text-right mt-1" x-text="msg.time"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>

                <!-- Typing Animation Indicator -->
                <div x-show="isTyping" x-cloak class="flex items-center gap-2 text-xs text-slate-400 pl-11">
                    <div class="flex gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-bounce"></span>
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-bounce [animation-delay:0.2s]"></span>
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-bounce [animation-delay:0.4s]"></span>
                    </div>
                    <span>NARA sedang merangkai respons...</span>
                </div>
            </div>

            <!-- Input Bar -->
            <div class="p-4 bg-white border-t border-slate-200">
                <form @submit.prevent="sendMessage()" class="flex items-center gap-2">
                    <input type="text" x-model="inputText" placeholder="Ketik curhatmu, tanya tips rehat, atau cerita apa saja ke NARA..." class="flex-1 rounded-2xl border-slate-200 text-xs py-3 px-4 focus:border-emerald-500 focus:ring-emerald-500 placeholder:text-slate-400">
                    <button type="submit" :disabled="!inputText.trim() || isTyping" class="px-5 py-3 rounded-2xl bg-emerald-700 hover:bg-emerald-800 disabled:opacity-50 text-white font-bold text-xs shadow-md shadow-emerald-700/20 transition flex items-center gap-1.5 flex-shrink-0">
                        <span>Kirim</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                </form>
            </div>

        </div>

    </div>
</x-app-layout>

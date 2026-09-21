<div x-data="{
    open: false,
    inputText: '',
    isTyping: false,
    messages: [
        {
            sender: 'nara',
            message: 'Halo! Ada yang lagi bikin kepalamu penuh atau mau tanya rekomendasi cara rehat hari ini?',
            time: '{{ date('H:i') }}'
        }
    ],

    sendMessage(textToSend = null) {
        const text = textToSend || this.inputText.trim();
        if (!text) return;

        this.messages.push({
            sender: 'user',
            message: text,
            time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
        });

        this.inputText = '';
        this.isTyping = true;
        this.$nextTick(() => {
            if (this.$refs.miniChatContainer) {
                this.$refs.miniChatContainer.scrollTop = this.$refs.miniChatContainer.scrollHeight;
            }
        });

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
                this.$nextTick(() => {
                    if (this.$refs.miniChatContainer) {
                        this.$refs.miniChatContainer.scrollTop = this.$refs.miniChatContainer.scrollHeight;
                    }
                });
            }
        })
        .catch(() => {
            this.isTyping = false;
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
    }
}" class="fixed bottom-6 right-6 z-40">

    <!-- Chat Pop-up Window -->
    <div x-show="open"
         x-cloak
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 translate-y-8 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-8 scale-95"
         class="mb-3 w-80 sm:w-96 h-[480px] bg-white rounded-[2rem] shadow-2xl border border-slate-200 overflow-hidden flex flex-col">

        <!-- Header -->
        <div class="px-5 py-3.5 bg-gradient-to-r from-[#557FA8] via-[#4D769E] to-[#436B92] text-white flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-white/20 flex items-center justify-center font-black text-sm">
                    N
                </div>
                <div>
                    <h4 class="font-bold text-xs">Chat NARA Assistant</h4>
                    <span class="text-[10px] text-white/80 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>Siap Mendengarkan</span>
                    </span>
                </div>
            </div>
            <div class="flex items-center gap-1">
                <a href="{{ route('chat.index') }}" title="Buka Layar Penuh" class="text-white/80 hover:text-white p-1.5 text-xs rounded-lg hover:bg-white/10 transition">
                    <x-icon name="sparkle" class="w-3.5 h-3.5" />
                </a>
                <button @click="open = false" class="text-white/80 hover:text-white p-1.5 text-base font-bold leading-none rounded-lg hover:bg-white/10 transition">&times;</button>
            </div>
        </div>

        <!-- Chat History -->
        <div x-ref="miniChatContainer" class="flex-1 p-4 overflow-y-auto space-y-3 bg-[#F8F6F0] text-xs">
            <template x-for="(msg, idx) in messages" :key="idx">
                <div>
                    <template x-if="msg.sender === 'nara'">
                        <div class="flex items-start gap-2.5">
                            <div class="w-6 h-6 rounded-lg bg-nara-600 text-white flex items-center justify-center text-[10px] font-bold flex-shrink-0 mt-0.5">
                                N
                            </div>
                            <div class="space-y-1.5 max-w-[85%]">
                                <div class="bg-white p-3 rounded-2xl rounded-tl-none border border-slate-200/80 shadow-xs text-slate-800 leading-relaxed whitespace-pre-line">
                                    <p x-text="msg.message"></p>
                                    <span class="block text-[9px] text-slate-400 text-right mt-1 font-mono" x-text="msg.time"></span>
                                </div>
                                <template x-if="msg.quick_replies && msg.quick_replies.length > 0">
                                    <div class="flex flex-wrap gap-1">
                                        <template x-for="qr in msg.quick_replies" :key="qr">
                                            <button type="button"
                                                    @click="handleQuickReply(qr)"
                                                    :class="isActionLink(qr) ? 'bg-teal-50 text-teal-800 border-teal-300 font-bold' : 'bg-white text-slate-700 border-slate-200'"
                                                    class="text-[10px] font-semibold px-2.5 py-1 rounded-xl border hover:bg-slate-50 transition cursor-pointer flex items-center gap-1 shadow-2xs">
                                                <span x-text="qr"></span>
                                                <span x-show="!isActionLink(qr)">&rarr;</span>
                                            </button>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    <template x-if="msg.sender === 'user'">
                        <div class="flex justify-end">
                            <div class="bg-nara-600 text-white p-3 rounded-2xl rounded-tr-none shadow-xs leading-relaxed max-w-[85%]">
                                <p x-text="msg.message"></p>
                                <span class="block text-[9px] text-white/70 text-right mt-1 font-mono" x-text="msg.time"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            <!-- Mini Typing Indicator -->
            <div x-show="isTyping" class="flex items-center gap-1.5 text-[10px] text-slate-400 pl-8">
                <span class="w-1 h-1 rounded-full bg-nara-600 animate-bounce"></span>
                <span class="w-1 h-1 rounded-full bg-nara-600 animate-bounce [animation-delay:0.2s]"></span>
                <span class="w-1 h-1 rounded-full bg-nara-600 animate-bounce [animation-delay:0.4s]"></span>
                <span>NARA merespons...</span>
            </div>
        </div>

        <!-- Mini Input Form -->
        <div class="p-3 bg-white border-t border-slate-100">
            <form @submit.prevent="sendMessage()" class="flex items-center gap-2">
                <input type="text" x-model="inputText" placeholder="Curhat / tanya rehat..." class="flex-1 rounded-xl border-slate-200 text-xs py-2 px-3 focus:border-nara-500 focus:ring-nara-500">
                <button type="submit" :disabled="!inputText.trim() || isTyping" class="p-2.5 rounded-xl bg-nara-600 hover:bg-nara-700 disabled:opacity-50 text-white text-xs font-bold transition">
                    <x-icon name="send" class="w-3.5 h-3.5" />
                </button>
            </form>
        </div>

    </div>

    <!-- Circular Floating Button -->
    <button @click="open = !open" class="w-14 h-14 rounded-full bg-gradient-to-tr from-[#557FA8] to-[#436B92] hover:from-[#4D769E] hover:to-[#345577] text-white shadow-xl shadow-nara-900/20 flex items-center justify-center text-xl transition transform hover:scale-105 active:scale-95 border-2 border-white" aria-label="Buka Chat NARA">
        <span x-show="!open">
            <x-icon name="chat" class="w-6 h-6 text-white" />
        </span>
        <span x-show="open" x-cloak class="text-2xl font-bold leading-none">&times;</span>
    </button>
</div>

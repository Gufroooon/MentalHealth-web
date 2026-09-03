<?php

namespace App\Services;

use App\Models\ChatMessage;
use App\Models\DailyCheckin;
use App\Models\User;

class ChatService
{
    /**
     * Process user message, run intent detection & knowledge base matching, save and return response
     */
    public function respond(User $user, string $userText): array
    {
        $cleanText = strtolower(trim($userText));

        // Save user message
        $userMsg = ChatMessage::create([
            'user_id' => $user->id,
            'sender' => 'user',
            'message' => $userText,
        ]);

        // Get user latest signals for context
        $latestCheckin = DailyCheckin::with('signal')
            ->where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->first();

        // Normalize typos before intent matching
        $normalized = $this->normalizeTypos($cleanText);
        $textForIntent = $normalized['corrected'];

        // Only show "Maksud kamu..." prefix if an actual NARA intent keyword was corrected
        // (not just abbreviation/particle expansions like gw→gua, karna→karena)
        $naraIntentKeywords = [
            'stres', 'overthinking', 'cemas', 'takut', 'panik', 'sedih',
            'tidur', 'capek', 'lelah', 'ngantuk', 'begadang', 'pusing', 'pegal',
            'tugas', 'deadline', 'skripsi', 'kerjaan', 'kuliah',
            'sepi', 'kesepian', 'halo', 'hai', 'makasih', 'ngapain',
            'rehat', 'istirahat', 'rileks', 'senang', 'semangat', 'bahagia',
        ];
        $correctedWords = explode(' ', $textForIntent);
        $originalWords  = explode(' ', $cleanText);
        $hasIntentKeywordFixed = false;
        foreach ($correctedWords as $i => $cw) {
            $ow = $originalWords[$i] ?? '';
            if ($cw !== $ow && in_array($cw, $naraIntentKeywords)) {
                $hasIntentKeywordFixed = true;
                break;
            }
        }

        // Run deterministic intent & knowledge matching
        $botResponse = $this->generateBotResponse($textForIntent, $user, $latestCheckin);

        // Prepend clarification prefix only when a genuine keyword typo was fixed
        if ($hasIntentKeywordFixed) {
            $botResponse['message'] = "_(Sepertinya kamu maksud **\"{$normalized['readable']}\"** — aku jawab berdasarkan itu ya!)_\n\n" . $botResponse['message'];
        }


        // Save bot response
        $naraMsg = ChatMessage::create([
            'user_id' => $user->id,
            'sender' => 'nara',
            'message' => $botResponse['message'],
            'quick_replies_json' => $botResponse['quick_replies'] ?? [],
            'intent_detected' => $botResponse['intent'],
        ]);

        return [
            'user_message' => $userMsg,
            'nara_message' => $naraMsg,
            'quick_replies' => $botResponse['quick_replies'] ?? [],
        ];
    }

    /**
     * Normalize common Indonesian typos and abbreviations.
     * Returns ['corrected' => <normalized for intent>, 'readable' => <human-friendly version>]
     */
    private function normalizeTypos(string $text): array
    {
        // ── Layer 1: Kamus typo & singkatan umum Indonesia ──────────────────
        // Hanya kata-kata yang JELAS merupakan typo/singkatan, bukan kata umum
        $typoDict = [
            // Kata ganti & partikel — hanya bentuk singkatan/slang yang tidak ambigu
            'gw'       => 'gua', 'ak'       => 'aku',
            'lo'       => 'kamu', 'lu'      => 'kamu', 'elo'     => 'kamu',
            'yg'       => 'yang', 'dgn'     => 'dengan', 'utk'   => 'untuk',
            'jg'       => 'juga',
            'tp'       => 'tapi', 'ttg'     => 'tentang', 'krn'  => 'karena',
            'krna'     => 'karena', 'karna' => 'karena',
            'skrg'     => 'sekarang', 'skrng' => 'sekarang',
            'udh'      => 'sudah', 'udah'   => 'sudah', 'sdh'    => 'sudah',
            'blm'      => 'belum', 'blum'   => 'belum',
            'trus'     => 'terus', 'trs'    => 'terus',
            'emg'      => 'emang', 'emng'   => 'emang',
            'gmn'      => 'gimana', 'gmna'  => 'gimana', 'bgmn'  => 'gimana',
            'knp'      => 'kenapa', 'knapa' => 'kenapa',
            'bngt'     => 'banget', 'bgt'   => 'banget', 'bngat' => 'banget',
            'kyk'      => 'kayak',
            'gk'       => 'gak', 'ngk'     => 'gak',
            'tdk'      => 'tidak',
            'msh'      => 'masih',
            'ntr'      => 'nanti', 'nti'    => 'nanti',
            'aj'       => 'saja',
            'kalo'     => 'kalau', 'klo'    => 'kalau',
            'dlm'      => 'dalam',
            'sbnernya' => 'sebenernya', 'sbnrnya' => 'sebenernya',

            // Perasaan & kondisi — hanya yang jelas typo (bukan kata lain)
            'strss'    => 'stres', 'strees'  => 'stres', 'setres' => 'stres',
            'overtinking'  => 'overthinking', 'overthingking' => 'overthinking',
            'ovrthnk'  => 'overthinking',
            'cmas'     => 'cemas', 'cemes'   => 'cemas',
            'takuut'   => 'takut', 'tkut'    => 'takut',
            'pannik'   => 'panik',
            'bngun'    => 'bangun', 'bngn'   => 'bangun',
            'tidor'    => 'tidur', 'tidr'    => 'tidur',
            'bgdang'   => 'begadang', 'bgadang' => 'begadang',
            'ngntuk'   => 'ngantuk', 'ngntk' => 'ngantuk',
            'lmah'     => 'lemah', 'lemes'   => 'lemas',
            'cpek'     => 'capek', 'cpee'    => 'capek',
            'lelh'     => 'lelah', 'llah'    => 'lelah',
            'pgal'     => 'pegal', 'pgel'    => 'pegal',
            'puising'  => 'pusing', 'pusng'  => 'pusing',
            'frusrasi' => 'frustrasi',
            'sdiih'    => 'sedih', 'sdih'    => 'sedih',
            'kespian'  => 'kesepian',

            // Aktivitas & situasi
            'tgsa'     => 'tugas',
            'dline'    => 'deadline', 'dedline' => 'deadline', 'dedlain' => 'deadline',
            'skrip'    => 'skripsi', 'skrispsi' => 'skripsi',
            'krjaan'   => 'kerjaan', 'kerjaa' => 'kerjaan',
            'projek'   => 'proyek',

            // Sapaan & respons
            'hlao'     => 'halo', 'hallo'   => 'halo', 'helo'   => 'halo',
            'haai'     => 'hai', 'haii'     => 'hai',
            'oek'      => 'oke', 'okey'     => 'oke', 'okee'    => 'oke',
            'makasiih' => 'makasih', 'mksih' => 'makasih', 'mksh' => 'makasih',
            'thanks'   => 'makasih', 'thx'   => 'makasih',
            'siapp'    => 'siap', 'siappp'  => 'siap',
            'sipppp'   => 'sip', 'sipp'     => 'sip',

            // Meta — tanya tentang NARA & kuis/pertanyaan
            'ngpain'   => 'ngapain',
            'siapa lo' => 'kamu siapa', 'siapa lu' => 'kamu siapa',
            'lo siapa' => 'kamu siapa', 'lu siapa' => 'kamu siapa',
            'lo ngapain' => 'kamu ngapain', 'lu ngapain' => 'kamu ngapain',
            'lagi ngpain' => 'lagi ngapain',
            'bsa apa'  => 'bisa apa', 'bs apa'  => 'bisa apa',
            'bisa apaa' => 'bisa apa',
            'quiz'     => 'kuis', 'kuiz'    => 'kuis', 'quizz'  => 'kuis',
            'kuizz'    => 'kuis', 'quis'    => 'kuis', 'kues'   => 'kuis',
            'nanya'    => 'tanya', 'nanyain' => 'tanya', 'tanyain' => 'tanya',
            'prtanyaan' => 'pertanyaan', 'pertnyaan' => 'pertanyaan', 'ptanyaan' => 'pertanyaan',
        ];

        // ── Whitelist: kata umum Indonesia yang TIDAK boleh dikoreksi levenshtein ──
        // Penting: kata-kata ini valid dan tidak boleh dianggap typo meskipun
        // mirip secara edit-distance dengan keyword NARA
        $commonWords = array_flip([
            // Kata tanya & permintaan & kuis
            'beri', 'kasih', 'tolong', 'minta', 'coba', 'kira', 'tanya',
            'pertanyaan', 'jawaban', 'jawab', 'cerita', 'ceritain',
            'bilang', 'beritahu', 'info', 'saran', 'kata', 'kuis', 'quiz',
            'kuiz', 'trivia', 'refleksi', 'soal', 'tes', 'uji',

            // Kata kerja umum
            'bisa', 'mau', 'buat', 'perlu', 'harus', 'boleh', 'ingin',
            'lihat', 'tahu', 'tau', 'cari', 'bantu', 'mulai', 'lagi',
            'punya', 'ngerti', 'paham', 'jelas', 'buka', 'tutup',
            'kirim', 'simpan', 'hapus', 'ubah', 'tambah', 'kurang',
            // Kata benda umum
            'hari', 'waktu', 'cara', 'hal', 'data', 'nama', 'hasil',
            'orang', 'teman', 'sahabat', 'keluarga', 'plan', 'rencana',
            'topik', 'tema', 'soal', 'masalah', 'solusi', 'jawaban',
            // Kata sifat & keterangan
            'baru', 'lama', 'cepat', 'lambat', 'mudah', 'sulit', 'susah',
            'besar', 'kecil', 'panjang', 'pendek', 'bagus', 'jelek',
            'pagi', 'siang', 'sore', 'malam', 'besok', 'kemarin',
            'benar', 'salah', 'tepat', 'wajar', 'pantas', 'normal',
            // Kata sambung & partikel
            'dan', 'atau', 'tapi', 'karena', 'jadi', 'kalau', 'jika',
            'dengan', 'dari', 'untuk', 'tentang', 'soal', 'seperti',
            'yang', 'ini', 'itu', 'sini', 'sana', 'situ',
            'aku', 'gua', 'kamu', 'saya', 'dia', 'mereka', 'kita', 'kami',
            'tidak', 'bukan', 'jangan', 'sudah', 'belum', 'masih', 'akan',
            'saja', 'juga', 'pun', 'pasti', 'mungkin', 'kayak', 'gitu',
            'emang', 'gimana', 'kenapa', 'siapa', 'kapan', 'dimana', 'berapa',
            'nih', 'deh', 'dong', 'sih', 'kan', 'lah', 'nah', 'seru',
            // Sapaan & ekspresi yang sudah valid
            'makasih', 'terima', 'maaf', 'sorry', 'oke', 'siap', 'sip',
            'halo', 'hai', 'hey', 'selamat', 'baik',
            // Kondisi/emosi yang sudah benar ejaannya
            'sepi', 'sedih', 'senang', 'takut', 'panik', 'cemas',
            'stres', 'capek', 'lelah', 'ngantuk', 'pusing', 'pegal', 'loyo',
            'tidur', 'begadang', 'istirahat', 'rehat', 'rileks', 'tenang',
            'kesepian', 'sendiri', 'lemas', 'lemah', 'frustrasi', 'banget',
            // Topik NARA yang sudah benar ejaannya
            'tugas', 'deadline', 'skripsi', 'kerjaan', 'kuliah', 'kantor',
            'proyek', 'ujian', 'dosen', 'sekolah', 'numpuk', 'overthinking',
        ]);

        $words = explode(' ', $text);
        $correctedWords  = [];
        $readableWords   = [];
        $anyChanged      = false;

        foreach ($words as $word) {
            // Skip very short words (1–2 chars), angka, dan tanda baca
            if (strlen($word) <= 2 || is_numeric($word)) {
                $correctedWords[] = $word;
                $readableWords[]  = $word;
                continue;
            }

            // ── Layer 1: cek kamus langsung ─────────────────────────────────
            if (isset($typoDict[$word])) {
                $corrected = $typoDict[$word];
                $correctedWords[] = $corrected;
                $readableWords[]  = $corrected;
                if ($corrected !== $word) $anyChanged = true;
                continue;
            }

            // ── Jika kata ada di whitelist, langsung lewati (bukan typo) ────
            if (isset($commonWords[$word])) {
                $correctedWords[] = $word;
                $readableWords[]  = $word;
                continue;
            }

            // ── Layer 2: levenshtein terhadap keyword utama NARA ────────────
            // Aturan ketat: hanya koreksi jika:
            //   - Panjang kata ≥ 6 huruf DAN jarak ≤ 1, ATAU
            //   - Panjang kata ≥ 9 huruf DAN jarak ≤ 2
            // Ini mencegah kata biasa seperti "beri" → "sepi"
            $wordLen = strlen($word);
            $maxDist = 0;
            if ($wordLen >= 6)  $maxDist = 1;
            if ($wordLen >= 9)  $maxDist = 2;

            if ($maxDist === 0) {
                // Kata terlalu pendek untuk levenshtein — skip
                $correctedWords[] = $word;
                $readableWords[]  = $word;
                continue;
            }

            $naraKeywords = [
                'stres', 'overthinking', 'cemas', 'takut', 'panik', 'sedih',
                'tidur', 'capek', 'lelah', 'ngantuk', 'begadang', 'pusing', 'pegal',
                'tugas', 'deadline', 'skripsi', 'kerjaan', 'kuliah',
                'sepi', 'kesepian', 'sendiri',
                'halo', 'makasih', 'ngapain', 'gimana', 'kenapa',
                'rehat', 'istirahat', 'rileks', 'tenang',
                'senang', 'semangat', 'bahagia',
            ];

            $bestMatch = null;
            $bestDist  = PHP_INT_MAX;

            foreach ($naraKeywords as $keyword) {
                $dist = levenshtein($word, $keyword);
                if ($dist < $bestDist && $dist <= $maxDist) {
                    $bestDist  = $dist;
                    $bestMatch = $keyword;
                }
            }

            if ($bestMatch && $bestMatch !== $word) {
                $correctedWords[] = $bestMatch;
                $readableWords[]  = $bestMatch;
                $anyChanged       = true;
            } else {
                $correctedWords[] = $word;
                $readableWords[]  = $word;
            }
        }

        $correctedText = implode(' ', $correctedWords);
        $readableText  = implode(' ', $readableWords);

        return [
            'corrected' => $anyChanged ? $correctedText : $text,
            'readable'  => $anyChanged ? $readableText  : $text,
            'changed'   => $anyChanged,
        ];
    }


    private function generateBotResponse(string $text, User $user, ?DailyCheckin $latestCheckin): array
    {
        $firstName = explode(' ', $user->name)[0];
        $score = $latestCheckin ? $latestCheckin->overall_wellbeing_score : null;

        // ─────────────────────────────────────────────
        // 1. GREETING
        // ─────────────────────────────────────────────
        if (preg_match('/\b(halo|hai|hi|hei|hello|pagi|siang|sore|malam|assalamualaikum|waalaikumsalam|hey)\b/i', $text)) {
            $scoreText = $score
                ? "Sinyal kesejahteraanmu terakhir ada di angka **{$score} poin**."
                : "Kamu belum check-in hari ini, kalau mau kita bisa mulai dari sana.";
            return [
                'intent'  => 'greeting',
                'message' => "Halo {$firstName}! 🌿 Senang kamu mau ngobrol sama NARA.\n\n{$scoreText}\n\nAda yang lagi mengganjal di pikiran, atau mau cerita soal harimu?",
                'quick_replies' => [
                    'Gua lagi stres & overthinking',
                    'Badan capek & kurang tidur',
                    'Beban tugas lagi numpuk',
                    'Apa saja fitur di NARA?',
                ],
            ];
        }

        // ─────────────────────────────────────────────
        // 2. SIAPA KAMU / KAMU ITU APA
        // ─────────────────────────────────────────────
        if (preg_match('/\b(siapa kamu|kamu itu apa|kamu siapa|lo siapa|apa itu nara|nara itu apa|nara itu siapa|kamu apaan|emang kamu apa)\b/i', $text)) {
            return [
                'intent'  => 'identity',
                'message' => "Aku NARA — teman pendamping kesejahteraan hidupmu! 🌿\n\nBukan robot canggih, bukan AI berbayar. Aku bekerja secara **100% deterministik dan privat** — artinya ceritamu tidak pernah dikirim ke server manapun di luar sini.\n\nAku hadir untuk:\n- Jadi tempat curhat yang aman\n- Bantu urai overthinking & stres\n- Rekomendasikan cara rehat yang pas\n- Mencatat sinyal hidupmu setiap hari\n\nAnggap aja aku teman yang selalu ada — 24 jam, tanpa menghakimi. 😊",
                'quick_replies' => [
                    'Kamu bisa bantu apa aja?',
                    'Apakah data aku aman?',
                    'Mulai check-in hari ini',
                ],
            ];
        }

        // ─────────────────────────────────────────────
        // 3. KAMU BISA APA / BISA BANTUIN APA
        // ─────────────────────────────────────────────
        if (preg_match('/\b(kamu bisa apa|bisa bantu apa|bisa bantuin|apa yang bisa|lo bisa ngapain|fungsinya apa|gunanya apa|manfaatnya apa)\b/i', $text)) {
            return [
                'intent'  => 'capabilities',
                'message' => "Ini yang bisa aku bantu buat kamu, {$firstName}! 💡\n\n🧠 **Soal Pikiran & Perasaan**\nCurhat soal stres, overthinking, kecemasan, atau sekadar pengin didengar.\n\n😴 **Soal Tubuh & Rehat**\nSaran cara pemulihan, tips tidur lebih nyenyak, atau aktivitas fisik ringan.\n\n📊 **Pantau Sinyal Hidupmu**\nCheck-in 4 vektor harian (Pikiran, Tubuh, Sosial, Beban Hidup) dan lihat trennya.\n\n🔮 **Analisis Pola**\nCari tahu kenapa energimu naik-turun minggu ini dibanding minggu lalu.\n\n🤝 **Support Network**\nKirim sinyal ke sahabat/keluarga saat hari terasa berat.\n\nMau mulai dari mana?",
                'quick_replies' => [
                    'Gua mau curhat dulu',
                    'Lihat sinyal hidupku',
                    'Kasih saran rehat',
                ],
            ];
        }

        // ─────────────────────────────────────────────
        // 4. DATA PRIVACY / KEAMANAN DATA
        // ─────────────────────────────────────────────
        if (preg_match('/\b(data aman|privasi|privacy|data bocor|aman ga|aman gak|rahasia ga|konfidensial|keamanan data)\b/i', $text)) {
            return [
                'intent'  => 'privacy_assurance',
                'message' => "Tenang, {$firstName} — data kamu 100% aman di NARA! 🔒\n\n- ✅ Semua data tersimpan **lokal** di servermu sendiri\n- ✅ **Tidak ada API AI eksternal** (OpenAI, Gemini, dll) — tidak ada ceritamu yang dikirim ke luar\n- ✅ Kamu bisa **ekspor, hapus, atau wipe** seluruh datamu kapan saja di Pusat Privasi\n- ✅ Komunitas Pulse bersifat **anonim sepenuhnya**\n\nAku cuma tahu apa yang kamu ceritain ke aku, dan itu tetap di sini. 😊",
                'quick_replies' => [
                    'Buka Pusat Privasi',
                    'Ekspor data aku',
                    'Lanjut ngobrol',
                ],
            ];
        }

        // ─────────────────────────────────────────────
        // 5. NARA LAGI APA / KAMU NGAPAIN
        // ─────────────────────────────────────────────
        if (preg_match('/\b(lagi apa|lagi ngapain|ngapain|kamu ngapain|sedang apa|doing what)\b/i', $text)) {
            return [
                'intent'  => 'nara_activity',
                'message' => "Lagi nemenin kamu ngobrol, {$firstName}! 😄🌿\n\nItu kerjaan favorit NARA — dengerin cerita, bantu urai pikiran yang lagi penuh, atau cari cara rehat yang pas buat kamu.\n\nAda sesuatu yang lagi dirasain atau mau diobrolin?",
                'quick_replies' => [
                    'Cerita sedikit nih',
                    'Gua mau tanya soal NARA',
                    'Kasih saran rehat dong',
                ],
            ];
        }

        // ─────────────────────────────────────────────
        // 6. TANYA FITUR / TUTORIAL
        // ─────────────────────────────────────────────
        if (preg_match('/\b(fitur|gimana cara|cara kerja|bantu apa|nara itu|tutorial|modul|menu|halaman|apa itu check.?in|apa itu recovery|apa itu pulse|apa itu what.?if)\b/i', $text)) {
            return [
                'intent'  => 'explain_nara',
                'message' => "NARA punya beberapa fitur utama yang bisa kamu pakai, {$firstName}:\n\n1. 📊 **Check-in 4 Sinyal** — pantau Pikiran, Tubuh, Sosial, dan Beban Hidup harian\n2. 🔍 **What Changed?** — tau kenapa energimu naik/turun vs minggu lalu\n3. 🔮 **Pola & What-If** — lihat dampak deadline ke tidurmu & simulasi kebiasaan baru\n4. 🧪 **Recovery Lab** — rankin aktivitas rehat yang paling ampuh buatmu\n5. 🤝 **Lingkaran Support** — kirim sinyal minta ditemani sahabat secara privat\n6. 💬 **Chat NARA** — ngobrol sama aku kapan saja (ini yang lagi kamu pakai!)\n7. 🔒 **Pusat Privasi** — kontrol penuh atas datamu",
                'quick_replies' => [
                    'Catat check-in hari ini',
                    'Buka Recovery Lab',
                    'Coba What-If Simulator',
                ],
            ];
        }

        // ─────────────────────────────────────────────
        // 7. KABAR / "KAMU GIMANA" / "LAGI BAIK?"
        // ─────────────────────────────────────────────
        if (preg_match('/\b(kamu gimana|kamu baik|kamu oke|kabarmu|kabar kamu|apa kabar kamu|how are you|how r u)\b/i', $text)) {
            return [
                'intent'  => 'nara_wellbeing',
                'message' => "Aku baik-baik aja, {$firstName}! Makasih udah nanya ya, itu langka dan bikin hangat. 🥰🌿\n\nNARA selalu siap dan segar buat nemenin kamu. Justru yang lebih penting — **kamu sendiri gimana hari ini?**",
                'quick_replies' => [
                    'Gua lagi oke sih',
                    'Jujur, lagi agak berat',
                    'Gua lagi overthinking',
                ],
            ];
        }

        // ─────────────────────────────────────────────
        // 8. USER BILANG BAIK / FINE / SEHAT
        // ─────────────────────────────────────────────
        if (preg_match('/\b(baik(-baik)?|fine|oke(-oke)?|sehat|alright|gak papa|nggak papa|tidak papa|biasa aja|lumayan|alhamdulillah baik)\b/i', $text)
            && preg_match('/\b(gua|aku|saya|gw)\b/i', $text)) {
            return [
                'intent'  => 'user_doing_well',
                'message' => "Alhamdulillah, seneng banget dengernya {$firstName}! 🌿✨\n\nJaga ritme yang baik ini ya. Kalau ada momen di mana hari terasa lebih berat, NARA selalu ada buat nemenin.\n\nAda hal lain yang mau diobrolin atau sekadar cerita soal harimu?",
                'quick_replies' => [
                    'Ada yang mau aku ceritain',
                    'Catat check-in hari ini',
                    'Sampai nanti NARA!',
                ],
            ];
        }

        // ─────────────────────────────────────────────
        // 9. PUJIAN KE NARA
        // ─────────────────────────────────────────────
        if (preg_match('/\b(kamu (keren|bagus|hebat|mantap|the best|luar biasa|canggih)|nara (keren|bagus|hebat)|makasih nara|terima kasih nara|good job|nice|gokil|top banget)\b/i', $text)) {
            return [
                'intent'  => 'compliment',
                'message' => "Wah, makasih {$firstName}! 😊🌿 Itu bikin aku semangat.\n\nSenang bisa nemenin kamu. Ada lagi yang mau diobrolin hari ini?",
                'quick_replies' => [
                    'Ada yang mau aku ceritain',
                    'Kasih saran rehat dong',
                    'Sampai nanti NARA!',
                ],
            ];
        }

        // ─────────────────────────────────────────────
        // 10. FRUSTRASI / KESAL KE NARA
        // ─────────────────────────────────────────────
        if (preg_match('/\b(kamu (goblok|bego|bodoh|ga ngerti|gak ngerti|ngaco|salah|error)|percuma|ga berguna|gak berguna|susah amat|ribet|nyebelin)\b/i', $text)) {
            return [
                'intent'  => 'frustration_to_nara',
                'message' => "Maaf ya {$firstName}, aku emang masih terbatas dan belum sempurna. 🙏\n\nAku akan terus berusaha lebih baik. Kalau kamu mau coba lagi dengan kata yang berbeda, aku siap dengerin kok. Dan kalau lagi frustrasi — itu wajar banget, aku ngerti.",
                'quick_replies' => [
                    'Oke gua coba lagi',
                    'Gua lagi stres nih sebenernya',
                    'Gak apa-apa, lanjut',
                ],
            ];
        }

        // ─────────────────────────────────────────────
        // 11. PERPISAHAN / PAMIT
        // ─────────────────────────────────────────────
        if (preg_match('/\b(bye|dadah|sampai nanti|sampai jumpa|see you|selamat tinggal|makasih ya nara|makasih nara|thanks nara|keluar dulu|cabut dulu)\b/i', $text)) {
            return [
                'intent'  => 'farewell',
                'message' => "Sampai nanti, {$firstName}! 🌿\n\nIngat ya — apapun yang kamu rasain hari ini, itu valid. Jaga dirimu baik-baik, dan NARA selalu di sini kalau kamu butuh teman ngobrol lagi. 💚",
                'quick_replies' => [
                    'Balik lagi ah',
                    'Catat check-in dulu',
                ],
            ];
        }

        // ═══════════════════════════════════════════════════════════════════
        // KNOWLEDGE BASE — PERNYATAAN EMOSI & SITUASI SPESIFIK
        // ═══════════════════════════════════════════════════════════════════

        // ─────────────────────────────────────────────
        // 12. INGIN MENANGIS / MAU NANGIS
        // ─────────────────────────────────────────────
        if (preg_match('/\b(mau nangis|pengen nangis|nangis|nangis terus|nangis aja|nangis doang|air mata|netes air mata|nangis diem|nangis sendirian)\b/i', $text)) {
            return [
                'intent'  => 'want_to_cry',
                'message' => "Nangis itu bukan tanda kelemahan, {$firstName}. Itu cara tubuhmu melepaskan beban yang udah terlalu lama ditanggung. 💙\n\nBoleh nangis. Beneran boleh.\n\nAir mata itu seperti katup tekanan — kalau kamu tahan terus, justru makin berat. Setelah nangis, biasanya ada rasa sedikit lebih lega.\n\nKalau boleh aku tanya — ada kejadian tertentu yang bikin kamu sampe titik ini?",
                'quick_replies' => [
                    'Lagi banyak masalah nih',
                    'Gua ngerasa sendirian',
                    'Gua cape banget',
                    'Gak tau kenapa juga',
                ],
            ];
        }

        // ─────────────────────────────────────────────
        // 13. MERASA TIDAK BERGUNA / WORTHLESS
        // ─────────────────────────────────────────────
        if (preg_match('/\b(tidak berguna|ga berguna|gak berguna|merasa tidak berarti|ngerasa gak berarti|ga ada gunanya|gak ada artinya|percuma ada aku|buat apa hidup|hidup gua percuma|ngerasa useless|useless)\b/i', $text)) {
            return [
                'intent'  => 'worthless_feeling',
                'message' => "Stop sebentar, {$firstName}. Aku dengerin kamu.\n\nPerasaan 'tidak berguna' itu sering muncul bukan karena kamu memang tidak berarti — tapi karena kamu lagi dalam kondisi yang sangat berat dan otak kamu lagi dalam mode bertahan hidup.\n\n⚠️ **Kalau perasaan ini sudah mengganggu tidur, makan, atau aktivitas harian lebih dari 2 minggu**, itu tanda kamu perlu bicara dengan profesional — dan itu bukan hal yang memalukan.\n\nSekarang, satu hal dulu: **apa yang paling bikin kamu ngerasa begini hari ini?**",
                'quick_replies' => [
                    'Gua ngerasa gagal terus',
                    'Gua ngerasa sendirian',
                    'Gua kelelahan',
                    'Gua mau cerita lebih',
                ],
            ];
        }

        // ─────────────────────────────────────────────
        // 14. PIKIRAN UNTUK MENYAKITI DIRI / PUTUS ASA
        // ─────────────────────────────────────────────
        if (preg_match('/\b(mau mati|pengen mati|ingin mati|lebih baik mati|bunuh diri|nyakitin diri|self harm|nyiksa diri|gak mau hidup lagi|capek hidup|cape hidup|udah gak kuat)\b/i', $text)) {
            return [
                'intent'  => 'crisis_risk',
                'message' => "{$firstName}, aku senang kamu mau cerita ini ke aku. Itu butuh keberanian.\n\nApa yang kamu rasain sekarang itu sangat berat — dan kamu tidak harus menanggungnya sendirian.\n\n🆘 **Tolong hubungi bantuan profesional sekarang:**\n- **Into The Light Indonesia**: 119 ext 8\n- **Yayasan Pulih**: (021) 788-42580\n- **Hotline Kesehatan Jiwa Kemenkes**: 021-500-454\n\nAku ada di sini bersamamu. Mau ceritain apa yang lagi kamu rasain?",
                'quick_replies' => [
                    'Aku mau cerita dulu',
                    'Hubungi hotline sekarang',
                ],
            ];
        }

        // ─────────────────────────────────────────────
        // 15. MERASA SEDIH (pernyataan langsung)
        // ─────────────────────────────────────────────
        if (preg_match('/\b(lagi sedih|merasa sedih|ngerasa sedih|lagi murung|lagi duka|hati berat|hati sedih|sedih banget|sedih aja|gloomy|down)\b/i', $text)) {
            return [
                'intent'  => 'feeling_sad',
                'message' => "Aku dengerin kamu, {$firstName}. Sedih itu boleh — itu manusiawi sekali. 💙\n\nKadang kita gak perlu langsung 'sembuh' dari kesedihan. Yang perlu kita lakukan dulu adalah **mengakui bahwa kita memang sedang tidak baik-baik saja**.\n\nCoba satu hal kecil sekarang:\n✍️ Tulis di selembar kertas: *\"Hari ini aku sedih karena...\"* — dan lanjutkan dengan jujur.\n\nKamu tidak harus punya solusinya sekarang. Boleh cerita ke aku apa yang bikin kamu sedih?",
                'quick_replies' => [
                    'Ada masalah dengan orang yang aku sayang',
                    'Aku ngerasa gagal',
                    'Aku ngerasa sendirian',
                    'Capek aja sih sebenarnya',
                ],
            ];
        }

        // ─────────────────────────────────────────────
        // 16. MERASA CEMAS / TAKUT / PANIK (pernyataan)
        // ─────────────────────────────────────────────
        if (preg_match('/\b(lagi cemas|merasa cemas|lagi takut|merasa takut|lagi panik|serangan panik|panic attack|deg-degan|jantung berdegup|berdebar|gemetar|tangan dingin|sesak napas karena cemas)\b/i', $text)) {
            return [
                'intent'  => 'feeling_anxious',
                'message' => "Tarik napas dulu, {$firstName}. Aku di sini. 🌬️\n\nKalau kamu lagi panik atau cemas intens sekarang, coba **teknik 5-4-3-2-1**:\n- 👀 Sebutkan **5 hal** yang kamu lihat di sekitar\n- 👂 **4 hal** yang kamu dengar\n- 🤚 **3 hal** yang bisa kamu sentuh\n- 👃 **2 hal** yang bisa kamu cium\n- 👅 **1 hal** yang bisa kamu rasain di mulut\n\nIni membantu otakmu kembali ke momen saat ini dan keluar dari spiral cemas.\n\nSudah coba? Gimana perasaan kamu sekarang?",
                'quick_replies' => [
                    'Udah sedikit mendingan',
                    'Masih cemas banget nih',
                    'Ceritain penyebabnya',
                    'Kasih teknik lain dong',
                ],
            ];
        }

        // ─────────────────────────────────────────────
        // 17. MERASA MARAH / KESAL / FRUSTRASI (pernyataan)
        // ─────────────────────────────────────────────
        if (preg_match('/\b(lagi marah|ngerasa marah|kesal banget|frustrasi banget|emosi|naik darah|mau meledak|pengen teriak|teriak aja|pengen nyalahin|dongkol|bete|sebel)\b/i', $text)) {
            return [
                'intent'  => 'feeling_angry',
                'message' => "Kemarahan itu valid, {$firstName}. Itu tanda bahwa ada sesuatu yang penting bagimu yang lagi terganggu atau dilanggar. 🔥\n\n**Jangan pendam — tapi juga jangan meledak dulu.**\n\nCara aman melepas marah:\n1. 🏃 Gerakan fisik — jalan cepat, push-up, atau pukul bantal\n2. ✍️ Tulis semua yang bikin kamu marah — tanpa sensor\n3. 🌬️ Tarik napas dalam 4 detik, tahan 4 detik, hembuskan 8 detik\n\nSetelah agak reda, baru kita bisa bahas solusinya. Mau cerita apa yang bikin kamu semarah ini?",
                'quick_replies' => [
                    'Lagi konflik sama orang lain',
                    'Kerjaan/kuliah bikin frustrasi',
                    'Gua ngerasa diperlakukan tidak adil',
                    'Gua marah sama diri sendiri',
                ],
            ];
        }

        // ─────────────────────────────────────────────
        // 18. MERASA MALU / MINDER / RENDAH DIRI
        // ─────────────────────────────────────────────
        if (preg_match('/\b(malu banget|ngerasa malu|minder|rendah diri|kurang pede|gak pede|tidak percaya diri|insecure|ngerasa jelek|ngerasa bodoh|ngerasa gak mampu|gak percaya diri)\b/i', $text)) {
            return [
                'intent'  => 'low_self_esteem',
                'message' => "Perasaan minder itu sangat umum, {$firstName} — bahkan orang-orang yang kelihatan percaya diri pun sering merasakannya di dalam. 💙\n\nRasa tidak percaya diri sering bukan soal kemampuan, tapi soal **narasi yang kita ulang-ulang ke diri sendiri**.\n\nCoba tanya ini ke dirimu: *'Kalau temanku yang merasa begini, apa yang akan aku katakan ke dia?'*\n\nBiasanya kita jauh lebih baik memperlakukan orang lain daripada diri sendiri.\n\nApa yang bikin kamu ngerasa kurang percaya diri hari ini?",
                'quick_replies' => [
                    'Gua ngerasa lebih buruk dari orang lain',
                    'Gua sering dibanding-bandingin',
                    'Gua gagal di sesuatu',
                    'Gua gak tau kenapa juga',
                ],
            ];
        }

        // ─────────────────────────────────────────────
        // 19. KESEPIAN / MERASA SENDIRIAN (pernyataan kuat)
        // ─────────────────────────────────────────────
        if (preg_match('/\b(lagi sepi|ngerasa sepi|merasa sendirian|gak ada yang peduli|gak ada yang nemenin|ditinggal sendirian|ga ada teman|gak ada orang|lonely|alone)\b/i', $text)) {
            return [
                'intent'  => 'feeling_lonely',
                'message' => "Rasa sepi itu salah satu perasaan paling berat yang manusia rasakan, {$firstName}. Aku dengar kamu. 🌿\n\nDan fakta bahwa kamu mau cerita ini ke aku — itu sendiri udah sebuah langkah berani.\n\n**Kamu tidak benar-benar sendirian** — meski rasanya seperti itu.\n\nDi NARA ada fitur **Lingkaran Support** di mana kamu bisa kirim sinyal *'Hari ini lagi berat'* ke sahabat atau keluarga, tanpa perlu cerita detail.\n\nTapi sebelum itu — mau ceritain ke aku kenapa kamu ngerasa sendiri?",
                'quick_replies' => [
                    'Buka Lingkaran Support',
                    'Gua habis konflik sama teman',
                    'Gua ngerasa gak dimengerti',
                    'Gua habis putus',
                ],
            ];
        }

        // ─────────────────────────────────────────────
        // 20. PUTUS CINTA / PATAH HATI
        // ─────────────────────────────────────────────
        if (preg_match('/\b(putus|habis putus|baru putus|diputusin|mutusin|patah hati|broken heart|mantan|galau soal pacar|kangen mantan|susah move on|move on)\b/i', $text)) {
            return [
                'intent'  => 'heartbreak',
                'message' => "Patah hati itu sakitnya nyata banget, {$firstName}. Bukan lebay, bukan lemah. Otak kita memproses kehilangan hubungan seperti kehilangan fisik — jadi wajar kalau rasanya sakit. 💙\n\n**Yang perlu kamu tahu:**\n- Gak ada timeline yang 'benar' untuk move on\n- Kamu boleh berduka atas hubungan yang berakhir\n- Setiap hari yang berhasil kamu lewati itu pencapaian\n\n**Yang bisa membantu sekarang:**\n1. Kurangi melihat akun sosmed mantan\n2. Isi waktu dengan hal yang kamu suka (bukan untuk melupakan, tapi untuk bertumbuh)\n3. Cerita ke orang yang kamu percaya\n\nMau cerita lebih?",
                'quick_replies' => [
                    'Gua gak bisa berhenti kepikiran',
                    'Gua mau tau cara move on',
                    'Gua kangen banget sama dia',
                    'Gua ngerasa sendirian sekarang',
                ],
            ];
        }

        // ─────────────────────────────────────────────
        // 21. MASALAH KELUARGA / KONFLIK ORANG TUA
        // ─────────────────────────────────────────────
        if (preg_match('/\b(masalah keluarga|berantem sama orang tua|konflik sama bokap|konflik sama nyokap|orang tua gak ngerti|ortu gak paham|dimarahin ortu|ortu cerewet|ortu toksik|keluarga toxic|toxic family|rumah gak nyaman|gak betah di rumah)\b/i', $text)) {
            return [
                'intent'  => 'family_conflict',
                'message' => "Konflik keluarga itu melelahkan banget, {$firstName}, terutama kalau kamu tidak punya tempat lain untuk pergi atau bersandar. 💙\n\n**Yang penting diingat:**\n- Kamu bisa mencintai keluargamu tapi tetap menetapkan batasan\n- Perbedaan pendapat dengan orang tua itu normal — tapi kamu punya hak untuk didengar\n- Kalau rumah terasa tidak aman secara emosional, itu sesuatu yang serius\n\n**Langkah kecil yang bisa dicoba:**\n1. Pilih waktu yang tepat untuk bicara — bukan saat keduanya lagi emosi\n2. Gunakan kalimat 'aku merasa...' bukan 'kamu selalu...'\n3. Cari 'safe space' sementara — teman, perpustakaan, dll\n\nApa yang lagi terjadi di keluargamu?",
                'quick_replies' => [
                    'Lagi berantem terus sama ortu',
                    'Gua ngerasa gak dimengerti',
                    'Rumah gak nyaman',
                    'Mau cerita lebih detail',
                ],
            ];
        }

        // ─────────────────────────────────────────────
        // 22. BURNOUT — KELELAHAN TOTAL
        // ─────────────────────────────────────────────
        if (preg_match('/\b(burnout|burn out|kelelahan total|capek mental|mental exhausted|gak ada energi|energi habis|baterai habis|kosong banget|hampa|mati rasa|gak ngerasa apa-apa|numb)\b/i', $text)) {
            return [
                'intent'  => 'burnout',
                'message' => "Itu yang kamu rasain namanya **burnout**, {$firstName} — dan itu kondisi nyata, bukan kelemahan. 🌿\n\nBurnout terjadi ketika kita terus-terusan memberi tanpa pernah mengisi ulang. Tanda-tandanya: hampa, tidak termotivasi, dan merasa bahkan istirahat pun tidak membantu.\n\n**Pemulihan burnout butuh waktu dan strategi:**\n1. 🛑 Kurangi beban — identifikasi mana yang *harus* dikerjakan vs mana yang bisa ditunda/didelegasi\n2. 🌱 Reconnect dengan hal yang pernah bikin kamu senang (bukan produktif)\n3. 😴 Prioritaskan tidur — bukan karena produktif, tapi karena tubuhmu butuh\n4. 🗣️ Cerita ke seseorang yang bisa mendengar tanpa menghakimi\n\nKalau burnout ini sudah berlangsung lama, pertimbangkan bicara ke konselor/psikolog ya.",
                'quick_replies' => [
                    'Gimana cara pulih dari burnout?',
                    'Gua gak bisa berhenti kerja',
                    'Buka Recovery Lab',
                    'Mau cerita kondisiku',
                ],
            ];
        }

        // ─────────────────────────────────────────────
        // 23. IMPOSTER SYNDROME
        // ─────────────────────────────────────────────
        if (preg_match('/\b(imposter syndrome|impostor syndrome|ngerasa pura-pura|takut ketahuan bodoh|gak layak|ngerasa gak pantas|gak seharusnya di sini|orang lain lebih pintar|semua orang lebih baik dari aku)\b/i', $text)) {
            return [
                'intent'  => 'imposter_syndrome',
                'message' => "Yang kamu rasain itu punya nama: **Imposter Syndrome** 🧠\n\nIni sangat umum — bahkan dialami oleh orang-orang yang sangat kompeten dan sukses. Penelitian menunjukkan 70% orang pernah mengalaminya.\n\n**Fakta yang perlu kamu pegang:**\n- Orang yang tidak kompeten justru jarang meragukan diri sendiri\n- Keraguan itu tanda bahwa kamu peduli dan ingin berkembang\n- Kamu ada di tempat itu karena ada alasannya\n\n**Yang bisa membantu:**\n- Catat pencapaianmu — sekecil apapun\n- Ingat bahwa semua orang juga belajar dan tidak tahu segalanya\n- Beda antara 'masih belajar' dan 'tidak kompeten'\n\nMau cerita situasi spesifik yang bikin kamu ngerasa seperti ini?",
                'quick_replies' => [
                    'Di kerjaan gua ngerasa gak kompeten',
                    'Di kuliah gua ngerasa tertinggal',
                    'Gua takut orang tau gua ga sepintar yang mereka kira',
                ],
            ];
        }

        // ─────────────────────────────────────────────
        // 24. MASALAH TIDUR / INSOMNIA
        // ─────────────────────────────────────────────
        if (preg_match('/\b(susah tidur|gak bisa tidur|insomnia|begadang terus|tidur malam tapi gak ngantuk|melek terus|bolak-balik di kasur|tidur tapi gak pulas|sering kebangun malam|mimpi buruk)\b/i', $text)) {
            return [
                'intent'  => 'sleep_problem',
                'message' => "Masalah tidur itu sangat mengganggu kualitas hidup secara keseluruhan, {$firstName}. 🌙\n\n**Tips berbasis bukti untuk tidur lebih baik:**\n\n⏰ **Rutinitas:** Tidur & bangun di jam yang sama setiap hari, termasuk akhir pekan\n📵 **Layar:** Matikan HP/laptop 30–60 menit sebelum tidur (blue light mengganggu melatonin)\n🌡️ **Suhu:** Kamar sedikit dingin membantu tidur lebih nyenyak\n☕ **Kafein:** Hindari kopi/teh setelah jam 2 siang\n🧠 **Pikiran:** Tulis semua yang mengganggu pikiranmu sebelum tidur — ini membantu otak 'melepaskan'\n\n⚠️ Kalau insomnia sudah lebih dari 3 minggu dan mengganggu aktivitas, pertimbangkan konsultasi ke dokter.",
                'quick_replies' => [
                    'Gua mikir terus sebelum tidur',
                    'Buka Recovery Lab',
                    'Gimana cara bikin ritual tidur?',
                    'Gua sering mimpi buruk',
                ],
            ];
        }

        // ─────────────────────────────────────────────
        // 25. MASALAH PRODUKTIVITAS / PROKRASTINASI
        // ─────────────────────────────────────────────
        if (preg_match('/\b(susah fokus|gak bisa fokus|males banget|prokrastinasi|nunda-nunda|gak produktif|rebahan terus|scrolling terus|distraksi terus|gak mood kerja|gak mood ngapa-ngapain|stuck)\b/i', $text)) {
            return [
                'intent'  => 'procrastination',
                'message' => "Prokrastinasi itu sering bukan soal malas, {$firstName} — seringnya itu soal **takut, cemas, atau overwhelmed**. 🧠\n\n**Kenapa kita nunda?**\n- Takut hasilnya tidak sempurna\n- Merasa tugasnya terlalu besar dan berat\n- Kondisi mental/fisik yang sedang tidak prima\n\n**Cara keluar dari prokrastinasi:**\n1. ⏱️ **Aturan 2 menit** — kalau bisa selesai dalam 2 menit, lakukan sekarang\n2. 🍅 **Teknik Pomodoro** — 25 menit kerja, 5 menit istirahat\n3. 🔢 **Task terkecil dulu** — bukan 'selesaikan laporan', tapi 'buka file-nya'\n4. 📵 Matikan notifikasi selama fokus\n\nApa tugas yang paling bikin kamu stuck sekarang?",
                'quick_replies' => [
                    'Skripsi/tugas besar nih',
                    'Kerjaan numpuk',
                    'Gua gak tau mau mulai dari mana',
                    'Kasih teknik fokus dong',
                ],
            ];
        }

        // ─────────────────────────────────────────────
        // 26. PERASAAN HAMPA / KOSONG / TIDAK BERMAKNA
        // ─────────────────────────────────────────────
        if (preg_match('/\b(ngerasa hampa|merasa kosong|gak ada tujuan|hidup gak bermakna|gak tau mau ngapain|ngerasa gak ada arah|ga ada motivasi|motivasi hilang|gak semangat sama sekali|apatis)\b/i', $text)) {
            return [
                'intent'  => 'emptiness',
                'message' => "Perasaan hampa dan kehilangan arah itu salah satu yang paling susah dijelaskan ke orang lain, {$firstName}. Tapi aku ngerti. 🌿\n\nPerasaan ini bisa muncul karena:\n- Terlalu lama dalam mode 'autopilot' tanpa hal yang benar-benar kamu inginkan\n- Kelelahan mental yang dalam (burnout)\n- Atau bisa jadi sinyal dari depresi yang perlu perhatian\n\n**Langkah kecil untuk mulai:**\n1. Jangan paksa diri untuk 'merasa semangat' — itu tidak realistis\n2. Coba satu aktivitas kecil yang dulu pernah bikin kamu senang\n3. Keluar dari ruangan, rasakan udara segar sebentar\n\n⚠️ Kalau perasaan ini sudah berlangsung lebih dari 2 minggu, sangat disarankan bicara ke psikolog.",
                'quick_replies' => [
                    'Gua ngerasa kayak gini sudah lama',
                    'Gimana cara menemukan motivasi?',
                    'Apa itu depresi?',
                    'Gua mau cerita lebih',
                ],
            ];
        }

        // ─────────────────────────────────────────────
        // 27. KHAWATIR MASA DEPAN / QUARTER LIFE CRISIS
        // ─────────────────────────────────────────────
        if (preg_match('/\b(khawatir masa depan|takut masa depan|gak tau mau jadi apa|bingung soal karir|bingung soal hidup|quarter life crisis|ngerasa ketinggalan|orang lain udah sukses|gua masih di sini aja|takut gagal|takut gak berhasil)\b/i', $text)) {
            return [
                'intent'  => 'future_anxiety',
                'message' => "Kecemasan soal masa depan itu sangat nyata, {$firstName} — terutama di usia yang penuh tekanan untuk 'berhasil'. 🌿\n\n**Yang perlu diingat:**\n- Timeline hidup setiap orang berbeda — tidak ada yang 'terlambat'\n- Apa yang kamu lihat di media sosial itu highlight reel, bukan reality show\n- Kebingungan di tahap ini justru tanda bahwa kamu sedang tumbuh\n\n**Pertanyaan yang lebih berguna daripada 'gua mau jadi apa':**\n- *'Aktivitas apa yang bikin waktu terasa cepat berlalu?'*\n- *'Nilai apa yang paling penting buatku?'*\n- *'Siapa orang yang hidupnya aku kagumi, dan kenapa?'*\n\nMau eksplorasi bareng?",
                'quick_replies' => [
                    'Gua bingung soal karir',
                    'Gua ngerasa ketinggalan dari teman-teman',
                    'Gua takut mengecewakan orang tua',
                    'Gimana cara cari tujuan hidup?',
                ],
            ];
        }

        // ─────────────────────────────────────────────
        // 28. NGERASA GAGAL / KECEWA SAMA DIRI SENDIRI
        // ─────────────────────────────────────────────
        if (preg_match('/\b(ngerasa gagal|merasa gagal|kecewa sama diri sendiri|ngecewain diri|ngecewain orang lain|gagal terus|sering gagal|gak bisa apa-apa|gak berhasil|usaha gua percuma)\b/i', $text)) {
            return [
                'intent'  => 'failure_feeling',
                'message' => "Kamu lagi keras banget sama dirimu sendiri, {$firstName}. Aku dengar itu. 💙\n\nKegagalan bukan cerminan nilai kamu sebagai manusia — itu cuma **informasi** bahwa ada sesuatu yang perlu disesuaikan.\n\nBahkan orang paling sukses di dunia punya daftar kegagalan yang panjang. Yang membedakan bukan mereka tidak pernah gagal — tapi bagaimana mereka merespons.\n\n**Satu pertanyaan:** Kalau temanmu yang bilang hal yang sama tentang dirinya sendiri, apa yang akan kamu katakan ke dia?\n\nSaatnya kamu perlakukan dirimu sama seperti kamu memperlakukan orang yang kamu sayangi.",
                'quick_replies' => [
                    'Gua gagal di ujian/tugas',
                    'Gua gagal di kerjaan',
                    'Gua ngecewain keluarga',
                    'Gua mau cerita lebih',
                ],
            ];
        }

        // ─────────────────────────────────────────────
        // 29. DIBANDING-BANDINGKAN / TEKANAN SOSIAL
        // ─────────────────────────────────────────────
        if (preg_match('/\b(dibanding-bandingin|selalu dibanding|kayak adik|kayak kakak|teman-teman udah|orang lain udah|semua orang udah|kok gua belum|ngerasa tertinggal|terlambat)\b/i', $text)) {
            return [
                'intent'  => 'comparison_pressure',
                'message' => "Dibanding-bandingin itu menyakitkan, {$firstName}. Terutama dari orang-orang yang seharusnya mendukungmu. 💙\n\n**Fakta tentang perbandingan:**\n- Kamu tidak pernah tahu perjuangan orang yang kamu bandingkan dengan dirimu\n- Setiap orang punya starting point, konteks, dan kapasitas yang berbeda\n- Perbandingan yang terus-menerus itu racun untuk mental health\n\n**Yang bisa membantu:**\n- Fokus pada **versi dirimu kemarin vs hari ini** — itu satu-satunya perbandingan yang adil\n- Batasi waktu di media sosial (minimal 30 menit per hari)\n- Ingat bahwa pencapaian orang lain tidak mengurangi pencapaianmu\n\nSiapa yang sering membandingkan kamu?",
                'quick_replies' => [
                    'Orang tua yang sering bandingin',
                    'Teman-teman di medsos',
                    'Gua yang bandingin diri sendiri',
                    'Mau cerita lebih',
                ],
            ];
        }

        // ═══════════════════════════════════════════════════════════════════
        // KNOWLEDGE BASE — PERTANYAAN MENTAL HEALTH (Q&A)
        // ═══════════════════════════════════════════════════════════════════

        // ─────────────────────────────────────────────
        // 30. APA ITU DEPRESI?
        // ─────────────────────────────────────────────
        if (preg_match('/\b(apa itu depresi|depresi itu apa|apakah aku depresi|gejala depresi|tanda-tanda depresi|ciri depresi|depresi tuh gimana|depresi atau sedih biasa)\b/i', $text)) {
            return [
                'intent'  => 'qa_depression',
                'message' => "**Apa itu Depresi?** 🧠\n\nDepresi bukan sekadar 'sedih' — ini kondisi medis nyata yang memengaruhi cara otak berfungsi.\n\n**Tanda-tanda depresi** (berlangsung ≥ 2 minggu):\n- Sedih, hampa, atau hopeless hampir setiap hari\n- Kehilangan minat pada hal yang dulu disukai\n- Perubahan nafsu makan atau berat badan\n- Susah tidur atau tidur terlalu banyak\n- Energi sangat rendah, mudah lelah\n- Sulit berkonsentrasi\n- Perasaan tidak berharga atau bersalah berlebihan\n- Pikiran tentang kematian atau menyakiti diri\n\n**Penting:** Depresi BISA diobati. Dengan bantuan profesional, banyak orang pulih sepenuhnya.\n\n⚠️ Kalau kamu merasakan 5+ tanda di atas, sangat disarankan konsultasi ke psikolog atau psikiater.",
                'quick_replies' => [
                    'Sepertinya gua punya tanda-tanda itu',
                    'Gimana cara cari psikolog?',
                    'Apa bedanya sedih biasa dan depresi?',
                    'Apa itu anxiety?',
                ],
            ];
        }

        // ─────────────────────────────────────────────
        // 31. APA ITU ANXIETY / KECEMASAN?
        // ─────────────────────────────────────────────
        if (preg_match('/\b(apa itu anxiety|anxiety itu apa|apa itu kecemasan|gangguan kecemasan|apakah aku anxiety|gejala anxiety|tanda anxiety|cara atasi anxiety|cara mengatasi cemas)\b/i', $text)) {
            return [
                'intent'  => 'qa_anxiety',
                'message' => "**Apa itu Anxiety (Kecemasan)?** 🧠\n\nAnxiety adalah respons alami tubuh terhadap ancaman — tapi pada gangguan kecemasan, respons ini terjadi secara berlebihan bahkan tanpa ancaman nyata.\n\n**Tanda-tanda gangguan kecemasan:**\n- Khawatir berlebihan tentang banyak hal\n- Sulit mengendalikan rasa cemas\n- Gelisah, tegang, mudah lelah\n- Sulit berkonsentrasi\n- Gejala fisik: jantung berdegup, keringat, gemetar, sesak napas\n- Menghindari situasi yang memicu cemas\n\n**Cara mengelola kecemasan:**\n- 🌬️ Latihan napas dalam (4-7-8 atau box breathing)\n- 🏃 Olahraga teratur — terbukti menurunkan hormon stres\n- ✍️ Journaling — tulis kecemasanmu, beri 'nama' pada rasa takut\n- 🧘 Mindfulness atau meditasi ringan\n- 🗣️ Terapi (CBT sangat efektif untuk anxiety)\n\nMau coba teknik pernapasan sekarang?",
                'quick_replies' => [
                    'Coba teknik napas sekarang',
                    'Gimana cara mindfulness?',
                    'Gua mau cari psikolog',
                    'Apa itu CBT?',
                ],
            ];
        }

        // ─────────────────────────────────────────────
        // 32. APA ITU OVERTHINKING? / CARA STOP OVERTHINKING
        // ─────────────────────────────────────────────
        if (preg_match('/\b(cara stop overthinking|cara mengatasi overthinking|kenapa aku overthinking|overthinking terus|susah berhenti mikir|pikiran muter-muter|pikiran ga bisa berhenti|rumination)\b/i', $text)) {
            return [
                'intent'  => 'qa_overthinking',
                'message' => "**Cara Mengatasi Overthinking** 🧠\n\nOverthinking adalah ketika pikiran kita berputar-putar pada masalah yang sama tanpa solusi — ini melelahkan dan biasanya tidak produktif.\n\n**Kenapa kita overthinking?**\nOtak mencoba mempersiapkan diri dari bahaya. Tapi kadang terlalu aktif di situasi yang sebenarnya tidak berbahaya.\n\n**Teknik yang terbukti efektif:**\n\n1. **🎯 Tentukan waktu 'khawatir'** — 15 menit sehari untuk memikirkan masalah, di luar itu alihkan pikiran\n2. **📝 Brain dump** — tulis semua yang ada di kepala, kosongkan otak ke kertas\n3. **❓ Tanya 'Apakah ini dalam kendaliaku?'** — kalau tidak, latih untuk melepaskan\n4. **🌬️ Grounding 5-4-3-2-1** — kembali ke momen saat ini\n5. **🏃 Gerakan fisik** — pecahkan siklus pikiran dengan aktivitas tubuh\n\nMau coba salah satunya sekarang?",
                'quick_replies' => [
                    'Coba grounding 5-4-3-2-1',
                    'Gua mau brain dump',
                    'Gua lagi overthinking soal sesuatu nih',
                    'Kasih teknik lain dong',
                ],
            ];
        }

        // ─────────────────────────────────────────────
        // 33. CARA JAGA KESEHATAN MENTAL
        // ─────────────────────────────────────────────
        if (preg_match('/\b(cara jaga (mental|kesehatan mental)|tips (mental health|kesehatan mental)|gimana cara sehat secara mental|self care itu apa|apa itu self care|cara merawat diri|mental hygiene)\b/i', $text)) {
            return [
                'intent'  => 'qa_mental_health_tips',
                'message' => "**Tips Menjaga Kesehatan Mental** 🌿\n\nKesehatan mental bukan sekadar 'tidak gila' — ini soal bagaimana kita berfungsi, merasakan, dan menjalani hidup sehari-hari.\n\n**Fondasi dasar (evidence-based):**\n\n😴 **Tidur cukup** — 7–9 jam, konsisten, prioritas utama\n🥗 **Makan bergizi** — gut-brain connection itu nyata\n🏃 **Olahraga** — 30 menit, 3–5x seminggu, terbukti seefektif antidepresan ringan\n🌿 **Keluar rumah** — paparan cahaya alami dan alam menurunkan kortisol\n📵 **Batasi medsos** — maksimal 1–2 jam per hari\n🗣️ **Koneksi sosial** — punya minimal 1 orang yang bisa kamu percaya\n✍️ **Journaling** — luapkan pikiran secara rutin\n🎯 **Tujuan kecil** — sense of accomplishment dari hal kecil itu penting\n\n⚠️ Kalau kamu sudah melakukan ini tapi masih kesulitan, itu tanda perlu bantuan profesional — dan itu wajar.",
                'quick_replies' => [
                    'Gimana cara mulai olahraga?',
                    'Tips journaling dong',
                    'Gimana cara batasi medsos?',
                    'Kapan harus ke psikolog?',
                ],
            ];
        }

        // ─────────────────────────────────────────────
        // 34. KAPAN HARUS KE PSIKOLOG / CARI BANTUAN?
        // ─────────────────────────────────────────────
        if (preg_match('/\b(kapan harus ke psikolog|perlu ke psikolog|butuh psikolog|cari psikolog|gimana cara cari psikolog|konseling itu apa|terapi itu perlu|bedanya psikolog dan psikiater|harga psikolog)\b/i', $text)) {
            return [
                'intent'  => 'qa_when_therapy',
                'message' => "**Kapan Perlu ke Psikolog?** 🗣️\n\nKamu tidak perlu menunggu 'parah dulu' untuk mencari bantuan. Psikolog bukan hanya untuk orang yang sedang krisis.\n\n**Tanda-tanda saatnya ke profesional:**\n- Gejala (sedih, cemas, dll) berlangsung > 2 minggu\n- Mengganggu pekerjaan, kuliah, atau hubungan sosial\n- Sudah mencoba banyak cara tapi tidak membantu\n- Ada pikiran menyakiti diri sendiri\n- Kamu merasa perlu bantuan (insting ini sering benar!)\n\n**Bedanya Psikolog vs Psikiater:**\n- **Psikolog** — ahli terapi & konseling, tidak bisa resepkan obat\n- **Psikiater** — dokter spesialis jiwa, bisa resepkan obat jika diperlukan\n\n**Cara cari psikolog terjangkau:**\n- Into The Light Indonesia\n- Yayasan Pulih (021-788-42580)\n- BPJS — bisa dirujuk ke poli jiwa RS\n- Into The Light: intothelightid.org",
                'quick_replies' => [
                    'Gua rasa gua butuh bantuan profesional',
                    'Gimana kalau gua gak mampu bayar?',
                    'Aku mau cerita kondisiku dulu',
                ],
            ];
        }

        // ─────────────────────────────────────────────
        // 35. APA ITU STRES / CARA MENGATASI STRES
        // ─────────────────────────────────────────────
        if (preg_match('/\b(apa itu stres|stres itu apa|cara mengatasi stres|cara atasi stres|tips stres|gimana cara stres berkurang|stres itu bahaya|kenapa aku stres terus)\b/i', $text)) {
            return [
                'intent'  => 'qa_stress',
                'message' => "**Memahami & Mengatasi Stres** 🌿\n\nStres adalah respons alami tubuh terhadap tekanan. Dalam dosis kecil, stres bisa memotivasi. Tapi stres kronis bisa merusak kesehatan.\n\n**Dampak stres kronis jika tidak ditangani:**\n- Sistem imun melemah\n- Gangguan tidur\n- Masalah pencernaan\n- Risiko penyakit jantung\n- Gangguan mental (depresi, anxiety)\n\n**Strategi mengelola stres (berbasis bukti):**\n1. 🌬️ **Napas dalam** — aktivasi sistem saraf parasimpatik (mode tenang)\n2. 🏃 **Olahraga** — membakar hormon stres (kortisol & adrenalin)\n3. 📝 **Journaling** — eksternalisasi kekhawatiran\n4. 🤝 **Koneksi sosial** — cerita ke orang yang dipercaya\n5. 🎯 **Kontrol apa yang bisa dikontrol** — lepaskan yang tidak bisa\n6. 😴 **Tidur cukup** — otak kita 'bersih' saat tidur\n\nApa sumber stresmu sekarang?",
                'quick_replies' => [
                    'Stres karena kerjaan/kuliah',
                    'Stres karena masalah hubungan',
                    'Stres tapi gak tau kenapa',
                    'Ajarin teknik napas dong',
                ],
            ];
        }

        // ─────────────────────────────────────────────
        // 36. PERTANYAAN TENTANG TIDUR & KESEHATAN
        // ─────────────────────────────────────────────
        if (preg_match('/\b(kenapa tidur penting|manfaat tidur|berapa lama tidur|kebutuhan tidur|cara tidur nyenyak|ritual tidur|sleep hygiene|tips tidur)\b/i', $text)) {
            return [
                'intent'  => 'qa_sleep',
                'message' => "**Panduan Tidur Sehat** 🌙\n\nTidur bukan 'buang waktu' — ini investasi terbaik untuk otak dan tubuhmu.\n\n**Berapa lama yang dibutuhkan?**\n- Remaja (14-17 tahun): 8–10 jam\n- Dewasa muda (18-25 tahun): 7–9 jam\n- Dewasa (26-64 tahun): 7–9 jam\n\n**Sleep Hygiene — Kebiasaan Tidur Sehat:**\n⏰ Tidur & bangun di jam yang **sama** setiap hari\n🌑 Buat kamar **gelap dan sejuk** (18–20°C ideal)\n📵 **No screen** 30–60 menit sebelum tidur\n☕ Hindari **kafein** setelah jam 2 siang\n🏃 **Olahraga** di pagi/siang, bukan malam hari\n✍️ Tulis **to-do list besok** sebelum tidur — kosongkan pikiran\n🧘 Buat **ritual menenangkan** — baca buku, mandi hangat, stretching\n\nHal mana yang sudah kamu lakukan?",
                'quick_replies' => [
                    'Gua susah tidur meski ngantuk',
                    'Pikiran aktif terus sebelum tidur',
                    'Gua sering begadang',
                    'Buka Recovery Lab',
                ],
            ];
        }

        // ─────────────────────────────────────────────
        // 37. JOURNALING / REFLEKSI — CARA & MANFAAT
        // ─────────────────────────────────────────────
        if (preg_match('/\b(cara journaling|tips journaling|manfaat journaling|apa itu journaling|gimana cara nulis jurnal|nulis jurnal itu apa|refleksi diri|cara refleksi)\b/i', $text)) {
            return [
                'intent'  => 'qa_journaling',
                'message' => "**Panduan Journaling untuk Kesehatan Mental** ✍️\n\nJournaling terbukti secara ilmiah membantu mengurangi stres, memproses emosi, dan meningkatkan kesadaran diri.\n\n**Cara mulai journaling:**\n\n🌅 **Morning Pages** — tulis 3 halaman bebas saat bangun tidur, apapun yang ada di pikiran\n🌙 **Evening Reflection** — 3 pertanyaan sebelum tidur:\n  - Apa yang berjalan baik hari ini?\n  - Apa yang bisa lebih baik?\n  - Apa yang aku syukuri?\n\n😔 **Emotion Journaling** — tulis: *'Aku ngerasa ___ karena ___, dan aku butuh ___'*\n\n**Tips:**\n- Tidak perlu sempurna atau rapih\n- Tidak ada yang akan membacanya\n- Konsistensi lebih penting dari panjangnya tulisan\n- Di NARA ada fitur **Jurnal Refleksi** yang bisa kamu pakai!\n\nMau coba sekarang?",
                'quick_replies' => [
                    'Buka Jurnal Refleksi NARA',
                    'Gua mau coba nulis sekarang',
                    'Kasih prompt journaling dong',
                ],
            ];
        }

        // ─────────────────────────────────────────────
        // 38. OLAHRAGA & MENTAL HEALTH
        // ─────────────────────────────────────────────
        if (preg_match('/\b(olahraga dan mental health|olahraga buat kesehatan mental|manfaat olahraga mental|kenapa olahraga penting|olahraga bisa atasi depresi|olahraga buat stres|mulai olahraga)\b/i', $text)) {
            return [
                'intent'  => 'qa_exercise_mental',
                'message' => "**Olahraga & Kesehatan Mental** 🏃\n\nIni bukan mitos — olahraga terbukti secara ilmiah sama efektifnya dengan antidepresan ringan untuk kasus depresi dan anxiety sedang.\n\n**Kenapa olahraga bantu mental health?**\n- 🧠 Melepaskan **endorfin** — hormon kebahagiaan alami\n- ⬇️ Menurunkan **kortisol** — hormon stres\n- 💤 Meningkatkan **kualitas tidur**\n- 🧠 Stimulasi pertumbuhan sel otak baru\n- 💪 Meningkatkan **kepercayaan diri**\n\n**Mulai dari yang kecil:**\n- 🚶 Jalan 20 menit sehari\n- 🧘 Yoga ringan 15 menit\n- 💃 Joget di kamar\n- 🏊 Berenang\n\n**Kuncinya:** Konsisten, bukan intens. 3x seminggu selama 30 menit lebih baik dari sekali seminggu 2 jam.",
                'quick_replies' => [
                    'Gimana kalau gua gak mood olahraga?',
                    'Olahraga apa yang paling mudah?',
                    'Buka Recovery Lab',
                ],
            ];
        }

        // ─────────────────────────────────────────────
        // 39. CARA KOMUNIKASI DENGAN ORANG LAIN / ASSERTIVE
        // ─────────────────────────────────────────────
        if (preg_match('/\b(cara ngomong ke orang|susah bilang tidak|susah nolak|gak bisa bilang no|people pleaser|suka ngalah terus|takut konfrontasi|cara komunikasi|cara ngungkapin perasaan)\b/i', $text)) {
            return [
                'intent'  => 'qa_communication',
                'message' => "Susah bilang 'tidak' dan menjadi people pleaser itu sangat menguras energi, {$firstName}. 💙\n\n**Kenapa kita susah bilang tidak?**\n- Takut mengecewakan orang lain\n- Takut konflik atau ditolak\n- Terbiasa diprioritaskan tapi tidak pernah memprioritaskan diri\n\n**Cara mulai asertif:**\n\n1. **Gunakan kalimat 'Aku':** *'Aku tidak bisa melakukan itu sekarang'* — bukan minta maaf\n2. **Tunda sebelum setuju:** *'Beri aku waktu untuk cek dulu'* — beri ruang untuk memutuskan\n3. **Tidak perlu alasan panjang:** 'Tidak' itu kalimat lengkap\n4. **Mulai dari yang kecil** — latihan menolak hal kecil dulu\n\n**Ingat:** Menjaga dirimu sendiri bukan egois — itu perlu.",
                'quick_replies' => [
                    'Gua sering ngorbanin diri buat orang lain',
                    'Gua takut dikira jahat kalau nolak',
                    'Gimana cara set batasan?',
                ],
            ];
        }

        // ─────────────────────────────────────────────
        // 40. CARA MINDFULNESS / MEDITASI
        // ─────────────────────────────────────────────
        if (preg_match('/\b(cara mindfulness|mindfulness itu apa|cara meditasi|meditasi untuk pemula|gimana cara meditasi|tips meditasi|manfaat meditasi|present moment)\b/i', $text)) {
            return [
                'intent'  => 'qa_mindfulness',
                'message' => "**Mindfulness untuk Pemula** 🧘\n\nMindfulness adalah kemampuan untuk hadir sepenuhnya di momen ini — tanpa menghakimi pikiran dan perasaan yang muncul.\n\n**Cara paling mudah mulai:**\n\n🌬️ **Meditasi napas (5 menit):**\n1. Duduk nyaman, pejamkan mata\n2. Fokus pada sensasi napas masuk dan keluar\n3. Ketika pikiran melayang (pasti akan melayang) — sadari, lalu kembali ke napas\n4. Lakukan 5 menit setiap pagi\n\n🚶 **Walking mindfulness:**\nSaat jalan, fokus pada sensasi kaki menyentuh tanah, suara di sekitar, udara di kulit.\n\n🍽️ **Makan mindful:**\nMakan tanpa HP — rasakan tekstur, rasa, aroma setiap suapan.\n\n**Benefit yang terbukti:**\n- Mengurangi stres dan kecemasan\n- Meningkatkan fokus\n- Memperbaiki kualitas tidur\n\nMau coba meditasi napas 5 menit sekarang?",
                'quick_replies' => [
                    'Iya, ajarin meditasi sekarang',
                    'Gua susah fokus saat meditasi',
                    'Rekomendasi app meditasi dong',
                ],
            ];
        }

        // ─────────────────────────────────────────────
        // 41. TEKANAN AKADEMIK / TAKUT NILAI JELEK
        // ─────────────────────────────────────────────
        if (preg_match('/\b(takut nilai jelek|nilai jelek|ipk turun|nilai anjlok|takut gagal ujian|ujian besok|deg-degan ujian|ujian tapi belum siap|presentasi besok|sidang skripsi|skripsi macet|bimbingan dosen)\b/i', $text)) {
            return [
                'intent'  => 'academic_pressure',
                'message' => "Tekanan akademik itu nyata banget, {$firstName}. Kamu tidak sendirian. 📚\n\n**Yang perlu diingat:**\nNilaimu bukan satu-satunya ukuran kecerdasanmu, dan bukan ukuran nilaimu sebagai manusia.\n\n**Strategi menghadapi tekanan akademik:**\n\n📅 **Persiapan:** Bagi materi jadi bagian kecil, jangan belajar semalam suntuk\n🧠 **Teknik belajar efektif:**\n- Active recall (bukan cuma baca ulang)\n- Spaced repetition\n- Pomodoro 25/5\n\n🌙 **Malam sebelum ujian:**\n- Tidur cukup lebih penting dari belajar semalaman\n- Siapkan perlengkapan dari malam sebelumnya\n- Breathing exercise sebelum tidur\n\n🎯 **Saat ujian:**\n- Baca soal dulu semua sebelum menjawab\n- Mulai dari yang bisa\n- Napas dalam kalau panik\n\nApa yang paling bikin kamu cemas soal akademik?",
                'quick_replies' => [
                    'Ujian besok tapi belum belajar',
                    'Skripsi gua lagi macet',
                    'IPK gua turun',
                    'Kasih teknik belajar efektif',
                ],
            ];
        }

        // ═══════════════════════════════════════════════════════════════════
        // INTENT YANG SUDAH ADA — DIPERLUAS DAN DIPERTAJAM
        // ═══════════════════════════════════════════════════════════════════

        // ─────────────────────────────────────────────
        // 42. OVERTHINKING & STRESS (umum)
        // ─────────────────────────────────────────────
        if (preg_match('/\b(stres|stress|overthinking|overthink|cemas|takut|panik|bingung|ruwet|gelisah|khawatir|was-was|anxiety|anxious)\b/i', $text)) {
            $stressResponses = [
                "Aku paham banget rasanya saat isi kepala lagi ramai, {$firstName}. Otak kita seringkali memproyeksikan skenario terburuk yang belum tentu terjadi.\n\nCoba tarik napas dalam 4 detik... tahan 4 detik... lalu hembuskan perlahan 6 detik. 🌬️\n\n**Langkah kecil NARA:**\n1. Tulis hal yang paling bikin cemas di kertas.\n2. Tanya dirimu: *'Mana satu hal yang BENAR-BENAR ada di dalam kendaliku hari ini?'*\n3. Istirahatkan mata dari layar selama 10 menit.",
                "Kepala penuh itu berat sekali, {$firstName}. 🧠\n\nKetika overthinking muncul, coba ingat ini: **pikiran bukan fakta**. Otak kita bisa menciptakan cerita yang terasa nyata tapi tidak selalu akurat.\n\nCoba teknik **'5 detik grounding'**: tekan kedua kakimu ke lantai, rasakan sensasi teksturnya. Ini membantu sistem sarafmu kembali ke mode tenang.",
            ];
            return [
                'intent'  => 'stress_overthinking',
                'message' => $stressResponses[array_rand($stressResponses)],
                'quick_replies' => [
                    'Bantu aku rehat 15 menit',
                    'Tugas kuliah/kerjaan bikin pusing',
                    'Mau tulis jurnal refleksi',
                    'Cara stop overthinking?',
                ],
            ];
        }

        // ─────────────────────────────────────────────
        // 43. CAPEK / LELAH / KURANG TIDUR (umum)
        // ─────────────────────────────────────────────
        if (preg_match('/\b(tidur|capek|lelah|ngantuk|loyo|lemes|badan|begadang|insomnia|pegal|pusing|cape|kelelahan)\b/i', $text)) {
            $sleepHours = $latestCheckin?->signal?->sleep_hours ?? null;
            $sleepText  = $sleepHours ? "Berdasarkan check-in terakhirmu, tidurmu ada di kisaran **{$sleepHours} jam**." : "";
            return [
                'intent'  => 'body_fatigue',
                'message' => "Tubuhmu lagi kirim sinyal alarm bahwa kapasitas fisikmu hampir habis, {$firstName}. {$sleepText}\n\n**Rekomendasi Pemulihan Cepat:**\n- Power nap 15–20 menit sebelum jam 3 sore.\n- Pasang hard stop kerja jam 22.00 dan jauhkan HP 20 menit sebelum tidur.\n- Minum segelas air putih hangat dan regangkan otot lehermu.\n\nKelelehan yang terlalu lama tanpa pemulihan bisa berujung pada burnout — yuk rawat tubuhmu ya!",
                'quick_replies' => [
                    'Buka Recovery Lab',
                    'Tips tidur lebih nyenyak',
                    'Gua butuh saran rehat sekarang',
                    'Apa itu burnout?',
                ],
            ];
        }

        // ─────────────────────────────────────────────
        // 44. DEADLINE / TUGAS / TEKANAN KERJAAN (umum)
        // ─────────────────────────────────────────────
        if (preg_match('/\b(tugas|deadline|ujian|kerja|skripsi|kantor|bos|dosen|kuliah|proyek|project|numpuk|kerjaan|presentasi)\b/i', $text)) {
            return [
                'intent'  => 'workload_pressure',
                'message' => "Tekanan tugas dan tenggat waktu memang sering bikin napas terasa sesak, {$firstName}.\n\nTapi ingat: kamu tidak harus menyelesaikan segalanya dalam satu jam ini.\n\n**Trik 'Single-Tasking' NARA:**\n1. Pilih SATU tugas paling mendesak.\n2. Pecah jadi langkah super kecil (misal: cukup buka dokumen & buat kerangka).\n3. Pasang timer 20 menit tanpa buka medsos.\n\nKamu bisa melewati ini. Satu langkah kecil itu cukup untuk sekarang.",
                'quick_replies' => [
                    'Tambah deadline ke timeline',
                    'Cara atasi prokrastinasi',
                    'Oke gua coba deh',
                    'Gua butuh support teman',
                ],
            ];
        }

        // ─────────────────────────────────────────────
        // 45. SEPI / KESEPIAN / HUBUNGAN SOSIAL (umum)
        // ─────────────────────────────────────────────
        if (preg_match('/\b(sepi|kesepian|sendiri|teman|sahabat|pacar|pasangan|berantem|konflik|keluarga|jauh|asing|lonely|alone)\b/i', $text)) {
            return [
                'intent'  => 'social_loneliness',
                'message' => "Rasa sepi atau gesekan dalam hubungan itu sangat menguras energi batin, {$firstName}. Membutuhkan kehadiran orang lain adalah hal yang sangat manusiawi — tidak ada yang salah dengan itu.\n\nDi NARA ada fitur **Lingkaran Support** di mana kamu bisa kirim sinyal *'Hari ini lagi agak berat'* ke sahabat atau keluargamu, tanpa harus cerita detail.\n\nKalau mau cerita lebih dulu ke aku, aku siap mendengar.",
                'quick_replies' => [
                    'Buka Lingkaran Support',
                    'Kirim sinyal ke sahabat',
                    'Gua habis konflik sama seseorang',
                    'Cerita lebih lanjut ke NARA',
                ],
            ];
        }

        // ─────────────────────────────────────────────
        // 46. SENANG / SYUKUR / MOOD POSITIF
        // ─────────────────────────────────────────────
        if (preg_match('/\b(senang|happy|lega|semangat|gembira|seru|suka|bahagia|alhamdulillah|bersyukur|sukses|berhasil|menang|puas|plong)\b/i', $text)) {
            $positiveResponses = [
                "Wah, senang banget dengernya {$firstName}! 🎉🌿\n\nMomen-momen seperti ini perlu dinikmati dan diingat. Saat energimu lagi bagus, manfaatkan untuk hal yang bermakna.\n\nJangan lupa apresiasi dirimu atas semua usaha yang sudah kamu lakukan ya!",
                "Itu energi yang bagus banget, {$firstName}! ✨🌿\n\nRiset menunjukkan bahwa **mencatat momen positif** — sekecil apapun — melatih otak untuk lebih mudah menemukan hal baik di hari-hari lain.\n\nMau catat rasa syukur ini di jurnal NARA?",
            ];
            return [
                'intent'  => 'positive_mood',
                'message' => $positiveResponses[array_rand($positiveResponses)],
                'quick_replies' => [
                    'Catat rasa syukur di jurnal',
                    'Lihat tren 14 hari terakhir',
                    'Sampai nanti NARA!',
                ],
            ];
        }

        // ─────────────────────────────────────────────
        // 47. SARAN REHAT / RELAKSASI
        // ─────────────────────────────────────────────
        if (preg_match('/\b(rehat|istirahat|rileks|relaksasi|santai|tenang|break|refreshing|menenangkan|cara rehat|saran rehat|butuh istirahat)\b/i', $text)) {
            $suggestions = [
                "**Box Breathing** 🌬️ Hirup 4 detik → Tahan 4 detik → Hembuskan 4 detik → Tahan 4 detik. Ulangi 4–6 kali. Teknik ini dipakai oleh Navy SEALs untuk menenangkan diri dalam situasi ekstrem.",
                "**Jalan santai 10–15 menit** di luar ruangan. Paparan cahaya alami dan gerakan fisik ringan terbukti menurunkan kadar kortisol (hormon stres) hingga 20%. Tinggalkan HP di dalam.",
                "**Progressive Muscle Relaxation:** Mulai dari kaki — kencangkan otot 5 detik, lalu lepaskan dan rasakan bedanya. Naik ke betis, paha, perut, dada, bahu, lengan, wajah. Butuh sekitar 10 menit.",
                "**'5 hal yang kusyukuri hari ini'** — tulis sekarang, sekecil apapun. Dari kopi yang enak hingga napas yang masih berjalan. Latihan gratitude terbukti mengubah baseline mood.",
            ];
            return [
                'intent'  => 'relaxation_tips',
                'message' => "Ini saran rehat dari NARA buat kamu sekarang, {$firstName}:\n\n{$suggestions[array_rand($suggestions)]}\n\nKalau mau saran yang lebih personal sesuai data sinyal hidupmu, buka **Recovery Lab** ya!",
                'quick_replies' => [
                    'Buka Recovery Lab',
                    'Kasih saran lain dong',
                    'Makasih, udah mendingan',
                ],
            ];
        }

        // ─────────────────────────────────────────────
        // 48. ACKNOWLEDGMENT / PENDEK
        // ─────────────────────────────────────────────
        if (preg_match('/^(oke|ok|baik|siap|sip|ya|iya|yep|yaps|yoi|noted|paham|ngerti|ooh|oh|ohh|hmm|hmmm|wah|woh|oke deh|iya deh|siap deh|gitu|gitu ya|oh gitu|oh oke|noted nih|paham deh)\.?$/i', $text)) {
            $responses = [
                "Oke {$firstName}! Kalau ada yang mau diceritain atau ditanyain, aku di sini ya. 🌿",
                "Siap! Lanjut kalau ada yang mau diobrolin, {$firstName}. Aku nemenin kok. 😊",
                "Noted! Ada lagi yang lagi dipikirin atau dirasain hari ini, {$firstName}?",
                "Oke, aku dengerin. Mau cerita lebih lanjut atau ada yang mau ditanyain? 🌿",
            ];
            return [
                'intent'  => 'acknowledgment',
                'message' => $responses[array_rand($responses)],
                'quick_replies' => [
                    'Ada yang mau aku ceritain',
                    'Gua baik-baik aja kok',
                    'Kasih saran rehat dong',
                ],
            ];
        }

        // ─────────────────────────────────────────────
        // 49. KONFIRMASI POSITIF (jawaban atas pertanyaan NARA)
        // ─────────────────────────────────────────────
        if (preg_match('/^(iya (dong|banget|sih|kan|nih|tuh)|bener|betul|tepat|persis|setuju|exactly|benar|ya bener|yep bener|iya tuh|itu dia|itu bener|bener banget|pas banget)\.?$/i', $text)) {
            $responses = [
                "Senang kita sepemahaman, {$firstName}! 😊 Mau lanjut cerita atau ada yang mau aku bantu?",
                "Nah, itu! Bagus kamu sadar itu. Langkah selanjutnya mau gimana, {$firstName}?",
                "Oke, aku catat ya. Kalau sudah siap untuk langkah berikutnya, aku di sini. 🌿",
            ];
            return [
                'intent'  => 'positive_confirmation',
                'message' => $responses[array_rand($responses)],
                'quick_replies' => [
                    'Gua mau cerita lebih',
                    'Kasih saran dong',
                    'Gua mau coba itu',
                ],
            ];
        }

        // ─────────────────────────────────────────────
        // 50. KONFIRMASI NEGATIF / KOREKSI (jawaban atas pertanyaan NARA)
        // ─────────────────────────────────────────────
        if (preg_match('/^(bukan|gak|tidak|nope|salah|gak gitu|bukan gitu|gak bener|kurang tepat|beda|bukan itu|gak sih|kurang|gak juga|engga|enggak)\.?$/i', $text)) {
            $responses = [
                "Oh, maaf kalau aku salah tangkap, {$firstName}! Boleh ceritain lebih detail gimana sebenernya? Aku mau ngerti yang kamu rasain dengan tepat. 🌿",
                "Oops, kayaknya aku belum ngerti dengan benar nih! Boleh dilurusin? Aku mau dengerin versi kamu. 😊",
                "Terima kasih udah betul-betulkan aku, {$firstName}! Coba ceritain lagi dengan cara yang berbeda? Aku siap dengerin.",
            ];
            return [
                'intent'  => 'negative_confirmation',
                'message' => $responses[array_rand($responses)],
                'quick_replies' => [
                    'Gini nih sebenernya...',
                    'Gua mau cerita dari awal',
                    'Boleh tanya yang lain dulu',
                ],
            ];
        }

        // ─────────────────────────────────────────────
        // 51. BERI AKU PERTANYAAN / KUIS / QUIZ / REFLEKSI DIRI
        // ─────────────────────────────────────────────
        if (preg_match('/\b(beri|kasih|minta|adain|mau|bikin)? ?(aku|gua|gw|saya|w)? ?(pertanyaan|soal|kuis|quiz|kuiz|quizz|kuizz|quis|trivia|tes|uji|refleksi)\b/i', $text)
            || preg_match('/\b(tanya (aku|gua|gw|saya)|tanyain (aku|gua|gw|saya)|tanya dong|tanyain dong|tes aku|uji aku|uji pemahaman)\b/i', $text)) {
            $quizOptions = [
                // 1. Mitos/Fakta: Orang Kuat
                [
                    'q' => "🧠 **Kuis Mitos atau Fakta NARA (1/12)**\n\n*\"Orang yang sehat atau kuat secara mental itu tidak pernah merasa sedih, cemas, atau lelah.\"*\n\nMenurutmu ini **BENAR (Fakta)** atau **SALAH (Mitos)**?",
                    'replies' => ['Salah (Mitos)', 'Benar (Fakta)', 'Ganti kuis lain'],
                ],
                // 2. Mitos/Fakta: Menangis
                [
                    'q' => "💧 **Kuis Mitos atau Fakta NARA (2/12)**\n\n*\"Menangis secara biologis melepaskan hormon stres (seperti adrenokortikotropik dan endorfin) yang membantu menenangkan sistem saraf.\"*\n\nMenurutmu ini **BENAR (Fakta)** atau **SALAH (Mitos)**?",
                    'replies' => ['Benar (Fakta)', 'Salah (Mitos)', 'Ganti kuis lain'],
                ],
                // 3. Mitos/Fakta: Depresi vs Malas
                [
                    'q' => "🧠 **Kuis Mitos atau Fakta NARA (3/12)**\n\n*\"Depresi klinis hanyalah rasa malas yang dibesar-besarkan dan bisa sembuh seketika hanya dengan 'berpikir positif'.\"*\n\nMenurutmu ini **BENAR (Fakta)** atau **SALAH (Mitos)**?",
                    'replies' => ['Salah (Mitos)', 'Benar (Fakta)', 'Ganti kuis lain'],
                ],
                // 4. Mitos/Fakta: Kafein & Tidur
                [
                    'q' => "☕ **Kuis Sleep Hygiene NARA (4/12)**\n\n*\"Kafein memiliki waktu paruh (half-life) 5–8 jam di dalam tubuh, sehingga minum kopi jam 4 sore masih bisa merusak fase tidur nyenyak (deep sleep) di malam hari.\"*\n\nMenurutmu ini **BENAR (Fakta)** atau **SALAH (Mitos)**?",
                    'replies' => ['Benar (Fakta)', 'Salah (Mitos)', 'Ganti kuis lain'],
                ],
                // 5. Multiple Choice: Panic Attack
                [
                    'q' => "🚨 **Kuis Pertolongan Pertama Mental Health (5/12)**\n\n*\"Apa langkah pertolongan pertama paling efektif saat serangan panik (panic attack) melanda?\"*\n\nA. Menahan napas selama mungkin\nB. Latihan napas lambat terkontrol & grounding 5-4-3-2-1\nC. Memaksa diri untuk tidak memikirkannya sama sekali\n\nPilih jawabanmu:",
                    'replies' => ['Jawaban B', 'Jawaban A', 'Jawaban C'],
                ],
                // 6. Multiple Choice: Stres vs Burnout
                [
                    'q' => "🔥 **Kuis Psikologi NARA (6/12)**\n\n*\"Apa ciri pembeda utama antara Stres Biasa dengan Burnout?\"*\n\nA. Stres ditandai keterlibatan emosi berlebihan (urgensi tinggi), sedangkan Burnout ditandai kehampaan emosi dan rasa mati rasa (disengagement)\nB. Stres hanya terjadi di sekolah\nC. Burnout bisa sembuh hanya dengan tidur 30 menit\n\nPilih jawabanmu:",
                    'replies' => ['Jawaban A', 'Jawaban B', 'Jawaban C'],
                ],
                // 7. Multiple Choice: Overthinking & CBT
                [
                    'q' => "🧩 **Kuis Cognitive Behavioral Therapy (7/12)**\n\n*\"Saat muncul suara di kepala: 'Pasti aku bakal gagal total', respon kognitif paling sehat adalah:\"*\n\nA. Mengutuk diri sendiri karena berpikiran negatif\nB. Menguji bukti nyata (fakta vs asumsi) dan merumuskan sudut pandang yang lebih seimbang\nC. Menghindari semua pekerjaan agar tidak gagal\n\nPilih jawabanmu:",
                    'replies' => ['Jawaban B', 'Jawaban A', 'Jawaban C'],
                ],
                // 8. Multiple Choice: Olahraga & Otak
                [
                    'q' => "🏃 **Kuis Neurobiologi NARA (8/12)**\n\n*\"Aktivitas fisik sedang (seperti jalan santai 20 menit) terbukti secara medis dapat:\"*\n\nA. Menurunkan kadar kortisol dan memicu pelepasan BDNF serta endorfin di otak\nB. Menghapus semua ingatan buruk seketika\nC. Tidak ada kaitannya dengan suasana hati\n\nPilih jawabanmu:",
                    'replies' => ['Jawaban A', 'Jawaban B', 'Jawaban C'],
                ],
                // 9. Mitos/Fakta: Toxic Positivity
                [
                    'q' => "⚠️ **Kuis Relasi & Empati NARA (9/12)**\n\n*\"Merespons teman yang sedang berduka dengan kalimat 'Jangan sedih dong, syukuri aja yang masih ada!' termasuk bentuk Toxic Positivity karena menyepelekan emosi alaminya.\"*\n\nMenurutmu ini **BENAR (Fakta)** atau **SALAH (Mitos)**?",
                    'replies' => ['Benar (Fakta)', 'Salah (Mitos)', 'Ganti kuis lain'],
                ],
                // 10. Mitos/Fakta: Multitasking
                [
                    'q' => "⚡ **Kuis Produktivitas Mental NARA (10/12)**\n\n*\"Otak manusia dirancang untuk melakukan multi-tasking berat (misal belajar sambil balas chat) tanpa menaikkan hormon stres atau menurunkan fokus.\"*\n\nMenurutmu ini **BENAR (Fakta)** atau **SALAH (Mitos)**?",
                    'replies' => ['Salah (Mitos)', 'Benar (Fakta)', 'Ganti kuis lain'],
                ],
                // 11. Refleksi: Respon Lelah
                [
                    'q' => "🌱 **Pertanyaan Refleksi Diri NARA (11/12)**\n\n*\"Ketika kamu merasa lelah atau gagal, apa reaksi pertamamu terhadap diri sendiri: menyalahkan diri dengan keras, atau memberi dirimu waktu dan ruang untuk jeda?\"*\n\nCoba jawab jujur apa yang sering terjadi ya!",
                    'replies' => ['Sering menyalahkan diri', 'Beri ruang untuk jeda', 'Campur aduk / bingung', 'Beri kuis lain'],
                ],
                // 12. Refleksi: Kebutuhan Terabaikan
                [
                    'q' => "🌿 **Pertanyaan Refleksi Diri NARA (12/12)**\n\n*\"Jika tubuh dan pikiranmu bisa minta satu hal ke kamu hari ini, apa yang paling mereka butuhkan?\"*\n\n(Pilih yang paling menyentuh kondisimu saat ini)",
                    'replies' => ['Tidur cukup tanpa layar', 'Divalidasi tanpa dihakimi', 'Kurangi standar terlalu tinggi', 'Beri kuis lain'],
                ],
            ];
            $selected = $quizOptions[array_rand($quizOptions)];
            return [
                'intent'  => 'reflection_quiz_question',
                'message' => $selected['q'],
                'quick_replies' => $selected['replies'],
            ];
        }

        // ─────────────────────────────────────────────
        // 52. JAWABAN KUIS / EVALUASI JAWABAN (BENAR / SALAH)
        // ─────────────────────────────────────────────
        if (preg_match('/\b(salah \(mitos\)|mitos|jawabannya salah|jawaban salah)\b/i', $text)) {
            return [
                'intent'  => 'quiz_answer_evaluation',
                'message' => "🎉 **Tepat sekali untuk pertanyaan mitos!**\n\nBanyak asumsi tentang kesehatan mental yang keliru di masyarakat. Memahami bahwa kerentanan emosional, batasan diri, dan kelelahan adalah respon manusiawi yang nyata adalah langkah awal menjaga resiliensi mental.\n\nMau lanjut kuis atau pertanyaan refleksi berikutnya?",
                'quick_replies' => [
                    'Beri aku pertanyaan kuis lagi',
                    'Aku mau curhat',
                    'Buka Recovery Lab',
                ],
            ];
        }

        if (preg_match('/\b(benar \(fakta\)|fakta|jawabannya benar|jawaban benar)\b/i', $text)) {
            return [
                'intent'  => 'quiz_answer_evaluation',
                'message' => "💡 **Mantap! Memahami fakta ilmiah mental health sangat membantu kita lebih welas asih pada diri sendiri.**\n\nKetika kita tahu cara kerja otak dan respon biologis tubuh terhadap stres, kita tidak mudah menyalahkan diri sendiri saat sedang merasa tidak baik-baik saja.\n\nMau coba kuis berikutnya?",
                'quick_replies' => [
                    'Beri aku pertanyaan kuis lagi',
                    'Aku mau tanya soal mental health',
                    'Catat rasa syukur di jurnal',
                ],
            ];
        }

        if (preg_match('/\b(jawaban b|pilih b)\b/i', $text) || ($text === 'b')) {
            return [
                'intent'  => 'quiz_answer_evaluation',
                'message' => "🎉 **100% TEPAT! Pilihan B adalah strategi berbasis bukti.**\n\nBaik dalam teknik pernapasan lambat 4-4-4, grounding 5-4-3-2-1, maupun pengujian bukti kognitif (CBT), kuncinya adalah **mengaktifkan sistem saraf parasimpatis dan mengarahkan fokus ke fakta nyata**.\n\nMau coba pertanyaan kuis lainnya?",
                'quick_replies' => [
                    'Beri aku pertanyaan kuis lagi',
                    'Ajari teknik grounding 5-4-3-2-1',
                    'Mau curhat ke NARA',
                ],
            ];
        }

        if (preg_match('/\b(jawaban a|pilih a)\b/i', $text) || ($text === 'a')) {
            return [
                'intent'  => 'quiz_answer_evaluation',
                'message' => "✨ **Pilihan A memiliki dasar ilmiah yang kuat untuk topik neurobiologi & stres vs burnout!**\n\nStres memicu hormon kortisol/adrenalin dengan intensitas tinggi, sementara burnout adalah titik di mana cadangan energi psikologis sudah habis total.\n\nMau eksplor kuis atau pertanyaan lainnya?",
                'quick_replies' => [
                    'Beri aku pertanyaan kuis lagi',
                    'Gimana cara pulih dari burnout?',
                    'Buka Recovery Lab',
                ],
            ];
        }

        if (preg_match('/\b(jawaban c|pilih c)\b/i', $text) || ($text === 'c')) {
            return [
                'intent'  => 'quiz_answer_evaluation',
                'message' => "💡 **Kurang tepat untuk opsi C.**\n\nMenghindari masalah atau memaksa diri tidak memikirkannya (*suppression*) justru terbukti memperparah kecemasan (*rebound effect*). Pendekatan yang lebih sehat adalah mengenali, menerima, dan menguji faktanya secara terukur.\n\nMau coba kuis lagi?",
                'quick_replies' => [
                    'Beri aku pertanyaan kuis lagi',
                    'Kasih tips atasi overthinking',
                    'Lanjut ngobrol',
                ],
            ];
        }

        if (preg_match('/\b(sering menyalahkan diri|menyalahkan diri sendiri|campur aduk|tidur cukup|divalidasi|kurangi standar)\b/i', $text)) {
            return [
                'intent'  => 'quiz_answer_evaluation',
                'message' => "💙 **Terima kasih sudah berani jujur pada dirimu sendiri, {$firstName}.**\n\nMenyadari apa yang sedang terjadi di dalam diri adalah 50% dari proses pemulihan. Ingat ya: kamu tidak harus selalu sempurna, dan kamu layak mendapatkan kebaikan dari dirimu sendiri sama seperti kamu bersikap baik ke orang lain.\n\nAda yang mau kamu ceritakan lebih dalam tentang ini?",
                'quick_replies' => [
                    'Aku mau cerita lebih lanjut',
                    'Catat rasa syukur di jurnal',
                    'Beri aku pertanyaan kuis lagi',
                ],
            ];
        }


        // ─────────────────────────────────────────────
        // 53. SELF-LOVE & PENERIMAAN DIRI
        // ─────────────────────────────────────────────
        if (preg_match('/\b(self love|self-love|mencintai diri|terima diri|menerima diri|berdamai dengan diri|kurang sayang diri|benci diri sendiri|benci sama diri)\b/i', $text)) {
            return [
                'intent'  => 'qa_self_love',
                'message' => "**Tentang Self-Love & Penerimaan Diri** 💚\n\nSelf-love bukan cuma beli barang kesukaan atau skincare, tapi tentang bagaimana kamu memperlakukan dirimu saat kamu sedang gagal atau berantakan.\n\n**3 Pilar Utama Self-Compassion (Dr. Kristin Neff):**\n1. **Self-Kindness:** Berbicara lembut pada diri sendiri, bukan mencaci saat salah.\n2. **Common Humanity:** Sadar bahwa kegagalan dan ketidaksempurnaan adalah bagian dari pengalaman semua manusia.\n3. **Mindfulness:** Mengamati rasa sakit tanpa melebih-lebihkannya atau menekannya.\n\n**Latihan praktis:** Saat suara kritik dalam kepalamu muncul, tanyakan: *'Apakah aku akan tega bicara sekejam ini ke sahabatku?'*",
                'quick_replies' => [
                    'Gua sering mengkritik diri sendiri',
                    'Gimana cara maafin kesalahan masa lalu?',
                    'Beri aku pertanyaan refleksi',
                ],
            ];
        }

        // ─────────────────────────────────────────────
        // 54. TRAUMA & INNER CHILD
        // ─────────────────────────────────────────────
        if (preg_match('/\b(trauma|inner child|luka masa kecil|luka batin|traumatik|ptsd|trauma masa lalu|healing inner child)\b/i', $text)) {
            return [
                'intent'  => 'qa_trauma_inner_child',
                'message' => "**Memahami Trauma & Inner Child** 🌿\n\nTrauma bukan hanya kejadian buruk yang terjadi padamu, tapi juga apa yang terjadi **di dalam dirimu** akibat kejadian tersebut ketika kamu tidak mendapat perlindungan atau ruang aman saat itu.\n\n**Inner Child** adalah bagian dari psikologis kita yang masih membawa luka, kebutuhan yang belum terpenuhi, atau rasa takut dari masa kecil.\n\n**Cara merawat diri saat luka lama terpicu:**\n- 🛡️ **Beri validasi:** *'Wajar kalau aku takut, dulu aku belum punya daya. Tapi sekarang aku sudah dewasa dan aman.'*\n- 🛑 **Kenali Trigger:** Catat situasi atau kata-kata apa yang sering membuatmu tiba-tiba drop atau defensif.\n- 🤝 **Bantuan Ahli:** Untuk trauma mendalam, terapi seperti EMDR, CBT, atau Somatic Experiencing sangat direkomendasikan bersama psikolog berlisensi.",
                'quick_replies' => [
                    'Gua sering terpicu sama hal sepele',
                    'Kapan harus ke psikolog?',
                    'Gua mau cerita masa lalu',
                ],
            ];
        }

        // ─────────────────────────────────────────────
        // 55. TOXIC POSITIVITY & HUBUNGAN TOXIC
        // ─────────────────────────────────────────────
        if (preg_match('/\b(toxic positivity|toksik|toxic relationship|hubungan toxic|red flag|gaslighting|manipulatif|dipaksa positif|jangan sedih terus)\b/i', $text)) {
            return [
                'intent'  => 'qa_toxic_positivity',
                'message' => "**Toxic Positivity & Batasan Sehat** ⚠️\n\n**Toxic Positivity** adalah pemaksaan untuk selalu berpikiran positif dan menolak/menyepelekan emosi negatif yang alami. Contoh: *'Udah jangan sedih, masih banyak yang lebih susah.'* Kalimat ini membuat orang merasa bersalah atas perasaan mereka yang valid.\n\n**Bedanya Support Sehat vs Toxic Positivity:**\n- ❌ *'Pasti ada hikmahnya, senyum dong!'* (Menolak emosi)\n- ✅ *'Pasti berat banget buat kamu. Aku di sini kalau kamu butuh teman cerita.'* (Memvalidasi emosi)\n\nKalau kamu berada di lingkungan atau hubungan yang sering gaslighting atau menyepelekan perasaanmu, kamu berhak memasang batas tegas (*boundaries*).",
                'quick_replies' => [
                    'Gua sering dibilang baperan / lebay',
                    'Gimana cara pasang batasan ke orang toxic?',
                    'Gua butuh tempat curhat yang aman',
                ],
            ];
        }

        // ─────────────────────────────────────────────
        // 56. PIKIRAN NEGATIF / NEGATIVE SELF-TALK
        // ─────────────────────────────────────────────
        if (preg_match('/\b(pikiran negatif|negative thinking|negative thoughts|self talk negatif|suara di kepala|mikir buruk terus|selalu mikir jelek|selalu curiga|prasangka buruk)\b/i', $text)) {
            return [
                'intent'  => 'qa_negative_thinking',
                'message' => "**Mengatasi Negative Self-Talk** 🧠\n\nOtak manusia punya kecenderungan alami bernama *Negativity Bias* — lebih mudah mengingat dan memikirkan hal buruk demi bertahan hidup di masa purba. Tapi di zaman sekarang, ini sering bikin kita over-anxious.\n\n**Metode 3C dari Terapi Kognitif (CBT):**\n1. **Catch it (Tangkap):** Sadari saat pikiran negatif muncul (misal: *'Pasti aku bakal gagal'*).\n2. **Check it (Uji):** Cari faktanya: *'Apa bukti nyata bahwa aku pasti gagal? Apa ada bukti sebaliknya?'*\n3. **Change it (Ubah):** Ubah jadi realistis: *'Aku mungkin cemas, tapi aku sudah berusaha dan bisa belajar dari prosesnya.'*\n\nMau kita coba uji satu pikiran yang lagi mengganggumu sekarang?",
                'quick_replies' => [
                    'Coba uji pikiran negatifku',
                    'Gua takut gagal banget',
                    'Kasih saran rehat',
                ],
            ];
        }

        // ─────────────────────────────────────────────
        // 57. TIDAK DIKENALI — FALLBACK JUJUR
        // ─────────────────────────────────────────────
        $notUnderstoodResponses = [
            "Hmm, aku kayaknya kurang nangkep maksud spesifikmu nih, {$firstName}. 😅\n\nCoba ceritain ulang dengan kata yang lebih santai? Atau pilih salah satu topik di bawah ya:",
            "Aku belum cukup paham yang kamu maksud, {$firstName} — maaf ya! 🙏\n\nSebagai pendamping mental wellness, aku paling siap bantu soal perasaan, overthinking, kelelahan, hubungan sosial, kuis refleksi, atau saran rehat. Mau coba yang mana?",
            "Wah, aku kurang nangkep nih {$firstName}. 🙈 Boleh diulang atau coba tanya dengan kata lain?\n\nKalau mau panduan cepat, kamu bisa pilih topik di bawah:",
        ];

        return [
            'intent'  => 'not_understood',
            'message' => $notUnderstoodResponses[array_rand($notUnderstoodResponses)],
            'quick_replies' => [
                'Gua lagi stres & overthinking',
                'Badan capek & kurang tidur',
                'Beri aku pertanyaan kuis',
                'Apa saja fitur di NARA?',
            ],
        ];
    }
}



<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Services\ChatService;
use App\Services\LifeSignalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function __construct(
        protected ChatService $chatService,
        protected LifeSignalService $lifeSignalService
    ) {}

    public function index()
    {
        $user = Auth::user();
        $messages = ChatMessage::where('user_id', $user->id)
            ->orderBy('created_at', 'asc')
            ->get();

        // If no message yet, generate initial welcome message from NARA
        if ($messages->isEmpty()) {
            $latestCheckin = $this->lifeSignalService->getLatestCheckin($user);
            $welcome = ChatMessage::create([
                'user_id' => $user->id,
                'sender' => 'nara',
                'message' => "Halo " . explode(' ', $user->name)[0] . "! 🌿 Aku NARA, teman pendamping kesejahteraan hidupmu.\n\nKamu bisa cerita apa saja tentang harimu, tumpukan tugas, overthinking, atau sekadar tanya rekomendasi cara rehat terbaik.\n\nAda hal yang mau diobrolin saat ini?",
                'quick_replies_json' => [
                    'Gua lagi stres & overthinking',
                    'Badan capek & kurang tidur',
                    'Beban tugas lagi numpuk',
                    'Apa saja fitur di NARA?',
                ],
                'intent_detected' => 'welcome',
            ]);
            $messages = collect([$welcome]);
        }

        $latestCheckin = $this->lifeSignalService->getLatestCheckin($user);

        return view('chat.index', compact('user', 'messages', 'latestCheckin'));
    }

    /**
     * Send message to NARA chatbot via AJAX
     */
    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $user = Auth::user();
        $result = $this->chatService->respond($user, $request->message);

        return response()->json([
            'success' => true,
            'user_message' => [
                'id' => $result['user_message']->id,
                'sender' => 'user',
                'message' => $result['user_message']->message,
                'time' => $result['user_message']->created_at->format('H:i'),
            ],
            'nara_message' => [
                'id' => $result['nara_message']->id,
                'sender' => 'nara',
                'message' => $result['nara_message']->message,
                'quick_replies' => $result['quick_replies'],
                'time' => $result['nara_message']->created_at->format('H:i'),
            ],
        ]);
    }

    /**
     * Clear chat history
     */
    public function clear(Request $request)
    {
        ChatMessage::where('user_id', Auth::id())->delete();
        return redirect()->route('chat.index')->with('success', 'Riwayat percakapan dengan NARA berhasil dibersihkan.');
    }
}

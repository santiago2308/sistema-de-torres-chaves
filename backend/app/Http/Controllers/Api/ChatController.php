<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Services\ChatbotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function __construct(
        private ChatbotService $chatbot,
    ) {
    }

    public function history(string $sessionId): JsonResponse
    {
        $session = ChatSession::where('session_id', $sessionId)->first();

        if (! $session) {
            return response()->json(['data' => []]);
        }

        $messages = $session->messages()->orderBy('created_at')->get([
            'role',
            'content',
            'created_at',
        ]);

        return response()->json(['data' => $messages]);
    }

    public function message(Request $request): JsonResponse
    {
        $request->validate([
            'session_id' => 'required|string|max:64',
            'message' => 'required|string|max:500',
        ]);

        $session = ChatSession::firstOrCreate(
            ['session_id' => $request->string('session_id')->toString()],
            ['visitor_name' => $request->input('name')],
        );

        ChatMessage::create([
            'chat_session_id' => $session->id,
            'role' => 'user',
            'content' => $request->string('message'),
        ]);

        $botResponse = $this->chatbot->respond($request->string('message'));

        ChatMessage::create([
            'chat_session_id' => $session->id,
            'role' => 'bot',
            'content' => $botResponse['message'],
        ]);

        return response()->json([
            'data' => [
                'message' => $botResponse['message'],
                'show_whatsapp' => $botResponse['show_whatsapp'],
                'session_id' => $session->session_id,
            ],
        ]);
    }
}
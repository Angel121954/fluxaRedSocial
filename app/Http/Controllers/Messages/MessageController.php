<?php

namespace App\Http\Controllers\Messages;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Events\NewMessage;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $activeConversation = null;
        $otherUser = null;

        if ($request->has('conv') && $request->conv) {
            $activeConversation = Conversation::with(['userA', 'userB', 'messages'])
                ->where('id', $request->conv)
                ->first();

            if ($activeConversation) {
                $otherUser = $activeConversation->otherUser($user);

                Message::where('conversation_id', $activeConversation->id)
                    ->where('sender_id', '!=', $user->id)
                    ->whereNull('read_at')
                    ->update(['read_at' => now()]);
            }
        }

        $conversations = Conversation::with(['userA', 'userB'])
            ->where(function ($q) use ($user) {
                $q->where('user_a_id', $user->id)->orWhere('user_b_id', $user->id);
            })
            ->get()
            ->sortByDesc(fn($c) => $c->lastMessage()?->created_at);

        return view('messages.index', compact('conversations', 'activeConversation', 'otherUser'));
    }

    public function show(Conversation $conversation): View
    {
        $user = auth()->user();

        if ($conversation->user_a_id !== $user->id && $conversation->user_b_id !== $user->id) {
            abort(403, 'No tienes acceso a esta conversación.');
        }

        $conversation->load(['userA', 'userB', 'messages']);
        $otherUser = $conversation->otherUser($user);

        Message::where('conversation_id', $conversation->id)
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $conversations = Conversation::with(['userA', 'userB'])
            ->where(function ($q) use ($user) {
                $q->where('user_a_id', $user->id)->orWhere('user_b_id', $user->id);
            })
            ->get()
            ->sortByDesc(fn($c) => $c->lastMessage()?->created_at);

        return view('messages.index', compact('conversation', 'otherUser', 'conversations'));
    }

    public function store(Request $request, Conversation $conversation): JsonResponse
    {
        $user = auth()->user();

        if ($conversation->user_a_id !== $user->id && $conversation->user_b_id !== $user->id) {
            return response()->json(['error' => 'No tienes acceso a esta conversación.'], 403);
        }

        $request->validate([
            'body' => 'required|string|max:2000',
        ]);

        $message = $conversation->messages()->create([
            'sender_id' => auth()->id(),
            'body'      => $request->body,
        ]);

        $message->load('sender');

        $recipientId = $conversation->user_a_id === auth()->id()
            ? $conversation->user_b_id
            : $conversation->user_a_id;

        broadcast(new NewMessage($message, $conversation->id, $recipientId))->toOthers();

        return response()->json([
            'id'         => $message->id,
            'body'       => $message->body,
            'sender'     => [
                'id'         => $message->sender->id,
                'name'       => $message->sender->name,
                'avatar_url' => $message->sender->avatar_url,
            ],
            'created_at' => $message->created_at->toIso8601String(),
        ]);
    }

    public function storeNewConversation(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'body'    => 'required|string|max:2000',
        ]);

        $otherUserId = $request->user_id;
        $userId      = auth()->id();

        if ($otherUserId === $userId) {
            return response()->json(['error' => 'No puedes enviarte mensajes a ti mismo.'], 422);
        }

        $conversation = Conversation::where(function ($query) use ($userId, $otherUserId) {
            $query->where(function ($q) use ($userId, $otherUserId) {
                $q->where('user_a_id', $userId)->where('user_b_id', $otherUserId);
            })->orWhere(function ($q) use ($userId, $otherUserId) {
                $q->where('user_a_id', $otherUserId)->where('user_b_id', $userId);
            });
        })->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'user_a_id' => $userId,
                'user_b_id' => $otherUserId,
            ]);
        }

        $message = $conversation->messages()->create([
            'sender_id' => $userId,
            'body'      => $request->body,
        ]);

        $message->load('sender');

        try {
            broadcast(new NewMessage($message, $conversation->id, $otherUserId))->toOthers();
        } catch (\Exception $e) {
            Log::error('Broadcast error (new conversation)', ['error' => $e->getMessage()]);
        }

        return response()->json([
            'conversation_id' => $conversation->id,
            'message' => [
                'id'         => $message->id,
                'body'       => $message->body,
                'sender'     => [
                    'id'         => $message->sender->id,
                    'name'       => $message->sender->name,
                    'avatar_url' => $message->sender->avatar_url,
                ],
                'created_at' => $message->created_at->toIso8601String(),
            ],
        ]);
    }

    public function getOrCreateConversation(string $username): JsonResponse
    {
        $user = \App\Models\User::where('username', $username)->first();

        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado.'], 404);
        }

        if ($user->id === auth()->id()) {
            return response()->json(['error' => 'No puedes iniciarte una conversación contigo.'], 422);
        }

        $currentUserId = auth()->id();
        $otherUserId   = $user->id;

        $conversation = Conversation::where(function ($query) use ($currentUserId, $otherUserId) {
            $query->where(function ($q) use ($currentUserId, $otherUserId) {
                $q->where('user_a_id', $currentUserId)->where('user_b_id', $otherUserId);
            })->orWhere(function ($q) use ($currentUserId, $otherUserId) {
                $q->where('user_a_id', $otherUserId)->where('user_b_id', $currentUserId);
            });
        })->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'user_a_id' => $currentUserId,
                'user_b_id' => $user->id,
            ]);
        }

        return response()->json([
            'conversation_id' => $conversation->id,
            'other_user'      => [
                'id'         => $user->id,
                'name'       => $user->name,
                'username'   => $user->username,
                'avatar_url' => $user->avatar_url,
            ],
        ]);
    }

    public function redirectToConversation(string $username)
    {
        $user = \App\Models\User::where('username', $username)->firstOrFail();

        if ($user->id === auth()->id()) {
            return redirect()->route('messages.index');
        }

        $conversation = Conversation::where(function ($q) use ($user) {
            $q->where('user_a_id', auth()->id())->where('user_b_id', $user->id);
        })->orWhere(function ($q) use ($user) {
            $q->where('user_a_id', $user->id)->where('user_b_id', auth()->id());
        })->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'user_a_id' => auth()->id(),
                'user_b_id' => $user->id,
            ]);
        }

        return redirect()->route('messages.index', ['conv' => $conversation->id]);
    }
}

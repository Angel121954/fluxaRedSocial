<?php

namespace App\Http\Controllers\Messages;

use App\Events\ConversationCreated;
use App\Events\NewMessage;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $user->load('profile');
        $profile = $user->profile;
        $activeConversation = null;
        $otherUser = null;
        $conversationId = $request->query('conv') ? (int) $request->query('conv') : null;

        $conversations = Conversation::with([
            'userA',
            'userB',
            'messages' => function ($q) {
                $q->with('sender')->oldest();
            },
        ])
            ->where(function ($q) use ($user) {
                $q->where('user_a_id', $user->id)->orWhere('user_b_id', $user->id);
            })
            ->get()
            ->sortByDesc(fn($c) => $c->messages->last()?->created_at);

        $activeMessages = collect();

        if ($conversationId) {
            $activeConversation = $conversations->firstWhere('id', $conversationId);
            if ($activeConversation) {
                $otherUser = $activeConversation->otherUser($user);

                Message::where('conversation_id', $activeConversation->id)
                    ->where('sender_id', '!=', $user->id)
                    ->whereNull('read_at')
                    ->update(['read_at' => now()]);

                $activeMessages = $activeConversation->messages()->with('sender')->oldest()->get();
            }
        }

        $userIds = $conversations->map(function ($conv) use ($user) {
            $other = $conv->otherUser($user);

            return $other?->id;
        })->filter()->unique()->values()->toArray();
        $userIds[] = $user->id;

        $profiles = Profile::whereIn('user_id', $userIds)->get()->keyBy('user_id');
        $usersWithProfiles = User::whereIn('id', $userIds)->get()->keyBy('id');

        $conversations->each(function ($conv) use ($user, $profiles, $usersWithProfiles) {
            $otherUser = $conv->otherUser($user);
            if ($otherUser && isset($usersWithProfiles[$otherUser->id])) {
                $other = $usersWithProfiles[$otherUser->id];
                $other->setRelation('profile', $profiles[$otherUser->id] ?? null);
                $conv->setRelation('otherChat', $other);
            }
        });

        return view('messages.index', compact('conversations', 'activeConversation', 'otherUser', 'profile', 'activeMessages'));
    }

    public function unreadCount(): JsonResponse
    {
        $count = Conversation::getUnreadGlobalCount();

        return response()->json(['count' => $count]);
    }

    public function markAsRead(Conversation $conversation): JsonResponse
    {
        $user = auth()->user();

        if ($conversation->user_a_id !== $user->id && $conversation->user_b_id !== $user->id) {
            return response()->json(['error' => 'No tienes acceso'], 403);
        }

        Message::where('conversation_id', $conversation->id)
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    public function markMessageAsRead(Message $message): JsonResponse
    {
        $user = auth()->user();

        $conversation = $message->conversation;
        if ($conversation->user_a_id !== $user->id && $conversation->user_b_id !== $user->id) {
            return response()->json(['error' => 'No tienes acceso'], 403);
        }

        if ($message->sender_id === $user->id) {
            return response()->json(['error' => 'No puedes leer tu propio mensaje'], 400);
        }

        if ($message->read_at === null) {
            $message->update(['read_at' => now()]);
        }

        return response()->json(['success' => true]);
    }

    public function show(Conversation $conversation): View
    {
        $user = auth()->user();
        $profile = Profile::where('user_id', $user->id)->first();

        if ($conversation->user_a_id !== $user->id && $conversation->user_b_id !== $user->id) {
            abort(403, 'No tienes acceso a esta conversación.');
        }

        Message::where('conversation_id', $conversation->id)
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $activeConversation = $conversation;
        $activeMessages = $conversation->messages()->with('sender')->oldest()->get();
        $otherUser = $conversation->otherUser($user);

        $conversations = Conversation::with([
            'userA',
            'userB',
            'messages' => function ($q) {
                $q->with('sender')->oldest();
            },
        ])
            ->where(function ($q) use ($user) {
                $q->where('user_a_id', $user->id)->orWhere('user_b_id', $user->id);
            })
            ->get()
            ->sortByDesc(fn($c) => $c->messages->last()?->created_at);

        return view('messages.index', compact('activeConversation', 'otherUser', 'conversations', 'profile', 'activeMessages'));
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
            'body' => $request->body,
        ]);

        $message->load('sender');

        $recipientId = $conversation->user_a_id === auth()->id()
            ? $conversation->user_b_id
            : $conversation->user_a_id;

        broadcast(new NewMessage($message, $conversation->id, $recipientId))->toOthers();

        return response()->json([
            'id' => $message->id,
            'body' => $message->body,
            'sender' => [
                'id' => $message->sender->id,
                'name' => $message->sender->name,
                'avatar_url' => $message->sender->avatar_url,
            ],
            'created_at' => $message->created_at->toIso8601String(),
        ]);
    }

    public function storeNewConversation(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'body' => 'required|string|max:2000',
        ]);

        $otherUserId = $request->user_id;
        $userId = auth()->id();

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

        $isNewConversation = ! $conversation;

        if ($isNewConversation) {
            $conversation = Conversation::create([
                'user_a_id' => $userId,
                'user_b_id' => $otherUserId,
            ]);
        }

        $message = $conversation->messages()->create([
            'sender_id' => $userId,
            'body' => $request->body,
        ]);

        $message->load('sender');

        try {
            if ($message) {
                broadcast(new NewMessage($message, $conversation->id, $otherUserId));
            }
            if ($isNewConversation) {
                broadcast(new ConversationCreated($conversation, $otherUserId, $message));
            }

            Log::info('=== BROADCAST END ===');
        } catch (\Exception $e) {
            Log::error('Broadcast error (new conversation)', ['error' => $e->getMessage()]);
        }

        return response()->json([
            'conversation_id' => $conversation->id,
            'message' => [
                'id' => $message->id,
                'body' => $message->body,
                'sender' => [
                    'id' => $message->sender->id,
                    'name' => $message->sender->name,
                    'avatar_url' => $message->sender->avatar_url,
                ],
                'created_at' => $message->created_at->toIso8601String(),
            ],
        ]);
    }

    public function getOrCreateConversation(string $username): JsonResponse
    {
        $user = User::where('username', $username)->first();

        if (! $user) {
            return response()->json(['error' => 'Usuario no encontrado.'], 404);
        }

        if ($user->id === auth()->id()) {
            return response()->json(['error' => 'No puedes iniciarte una conversación contigo.'], 422);
        }

        $currentUserId = auth()->id();
        $otherUserId = $user->id;

        $conversation = Conversation::where(function ($query) use ($currentUserId, $otherUserId) {
            $query->where(function ($q) use ($currentUserId, $otherUserId) {
                $q->where('user_a_id', $currentUserId)->where('user_b_id', $otherUserId);
            })->orWhere(function ($q) use ($currentUserId, $otherUserId) {
                $q->where('user_a_id', $otherUserId)->where('user_b_id', $currentUserId);
            });
        })->first();

        if (! $conversation) {
            $conversation = Conversation::create([
                'user_a_id' => $currentUserId,
                'user_b_id' => $user->id,
            ]);
        }

        return response()->json([
            'conversation_id' => $conversation->id,
            'other_user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'avatar_url' => $user->avatar_url,
            ],
        ]);
    }

    public function redirectToConversation(string $username)
    {
        $user = User::where('username', $username)->firstOrFail();

        if ($user->id === auth()->id()) {
            return redirect()->route('messages.index');
        }

        $conversation = Conversation::where(function ($q) use ($user) {
            $q->where('user_a_id', auth()->id())->where('user_b_id', $user->id);
        })->orWhere(function ($q) use ($user) {
            $q->where('user_a_id', $user->id)->where('user_b_id', auth()->id());
        })->first();

        $isNewConversation = ! $conversation;

        if ($isNewConversation) {
            $conversation = Conversation::create([
                'user_a_id' => auth()->id(),
                'user_b_id' => $user->id,
            ]);

            try {
                broadcast(new ConversationCreated($conversation, $user->id));
                \Log::info('ConversationCreated broadcast from redirectToConversation', [
                    'conversation_id' => $conversation->id,
                    'otherUserId' => $user->id,
                ]);
            } catch (\Exception $e) {
                \Log::error('Broadcast error in redirectToConversation', ['error' => $e->getMessage()]);
            }
        }

        return redirect()->route('messages.index', ['conv' => $conversation->id]);
    }
}

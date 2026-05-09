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

        if ($conversationId) {
            $activeConversation = Conversation::find($conversationId);
            if ($activeConversation && ($activeConversation->user_a_id === $user->id || $activeConversation->user_b_id === $user->id)) {
                $otherUser = $activeConversation->otherUser($user);

                Message::where('conversation_id', $activeConversation->id)
                    ->where('sender_id', '!=', $user->id)
                    ->whereNull('read_at')
                    ->update(['read_at' => now()]);

                $activeMessages = $activeConversation->messages()->with('sender')->oldest()->get();
            } else {
                $activeMessages = collect();
                $activeConversation = null;
            }
        } else {
            $activeMessages = collect();
        }

        $conversations = Conversation::with(['userA', 'userB'])
            ->withCount(['messages as unread_count' => function ($q) use ($user) {
                $q->where('sender_id', '!=', $user->id)
                  ->whereNull('read_at');
            }])
            ->where(function ($q) use ($user) {
                $q->where('user_a_id', $user->id)->orWhere('user_b_id', $user->id);
            })
            ->get()
            ->sortByDesc(function ($c) {
                $lastMsg = \App\Models\Message::where('conversation_id', $c->id)
                    ->orderByDesc('created_at')
                    ->first();
                return $lastMsg?->created_at;
            });

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
        $convId = request()->query('exclude_conv') ? (int) request()->query('exclude_conv') : null;
        $count = Conversation::getUnreadGlobalCount($convId);

        return response()->json(['count' => $count]);
    }

    public function setViewing(Request $request): JsonResponse
    {
        $request->validate([
            'conversation_id' => 'required|integer|exists:conversations,id',
        ]);

        $userId = auth()->id();
        $convId = (int) $request->conversation_id;

        // Check user is part of this conversation
        $conversation = Conversation::find($convId);
        if (! $conversation || ($conversation->user_a_id !== $userId && $conversation->user_b_id !== $userId)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Store in cache that user is viewing this conversation (expires in 2 minutes)
        \Illuminate\Support\Facades\Cache::put("user.{$userId}.viewing_conv", $convId, now()->addMinutes(2));

        return response()->json(['success' => true]);
    }

    public function clearViewing(): JsonResponse
    {
        $userId = auth()->id();
        \Illuminate\Support\Facades\Cache::forget("user.{$userId}.viewing_conv");

        return response()->json(['success' => true]);
    }

    public function markAsRead(Conversation $conversation): JsonResponse
    {
        $this->authorize('view', $conversation);

        $user = auth()->user();

        Message::where('conversation_id', $conversation->id)
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    public function markMessageAsRead(Message $message): JsonResponse
    {
        $this->authorize('view', $message);

        $user = auth()->user();

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

        $this->authorize('view', $conversation);

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
        ])
            ->where(function ($q) use ($user) {
                $q->where('user_a_id', $user->id)->orWhere('user_b_id', $user->id);
            })
            ->get()
            ->each(function ($conv) use ($user) {
                $conv->setRelation('messages', $conv->messages()->with('sender')->oldest()->get());
            })
            ->sortByDesc(fn($c) => $c->messages->last()?->created_at);

        return view('messages.index', compact('activeConversation', 'otherUser', 'conversations', 'profile', 'activeMessages'));
    }

    public function store(Request $request, Conversation $conversation): JsonResponse
    {
        $user = auth()->user();

        $request->validate([
            'body' => 'required|string|max:2000',
        ]);

        $recipientId = $conversation->user_a_id === auth()->id()
            ? $conversation->user_b_id
            : $conversation->user_a_id;

        // Check if sender can send (based on recipient's accept_messages setting)
        $recipientProfile = \App\Models\Profile::where('user_id', $recipientId)->first();
        $recipientAcceptsMessages = $recipientProfile && $recipientProfile->accept_messages;
        
        if (!$recipientAcceptsMessages) {
            return response()->json([
                'error' => 'Este usuario no acepta mensajes directos',
                'recipient_accepts_messages' => false,
            ], 403);
        }

        // Create message
        $message = $conversation->messages()->create([
            'sender_id' => auth()->id(),
            'body' => $request->body,
        ]);

        $message->load('sender');

        // Check if recipient is viewing this conversation (via cache)
        $recipientViewing = \Illuminate\Support\Facades\Cache::get("user.{$recipientId}.viewing_conv") === $conversation->id;
        
        if ($recipientViewing) {
            $message->update(['read_at' => now()]);
        }

        // Always broadcast (receiving is never blocked)
        try {
            broadcast(new NewMessage($message, $conversation->id, $recipientId))->toOthers();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Broadcast failed: ' . $e->getMessage());
        }

        return response()->json([
            'id' => $message->id,
            'body' => $message->body,
            'sender' => [
                'id' => $message->sender->id,
                'name' => $message->sender->name,
                'avatar_url' => $message->sender->avatar_url,
            ],
            'created_at' => $message->created_at->timezone('America/Bogota')->toIso8601String(),
            'recipient_accepts_messages' => true,
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

        $otherProfile = \App\Models\Profile::where('user_id', $otherUserId)->first();
        if (!$otherProfile || !$otherProfile->accept_messages) {
            return response()->json([
                'error' => 'Este usuario no acepta mensajes directos',
                'recipient_accepts_messages' => false,
            ], 403);
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

        // Always create message (so sender can see it)
        $message = $conversation->messages()->create([
            'sender_id' => $userId,
            'body' => $request->body,
        ]);

        $message->load('sender');

        // Always broadcast (receiving is never blocked by accept_messages)
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
                'created_at' => $message->created_at->timezone('America/Bogota')->toIso8601String(),
            ],
            'recipient_accepts_messages' => $otherProfile->accept_messages,
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

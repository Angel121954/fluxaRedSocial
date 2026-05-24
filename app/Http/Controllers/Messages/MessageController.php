<?php

namespace App\Http\Controllers\Messages;

use App\Events\UserBlocked;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Profile;
use App\Models\User;
use App\Services\MessageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function __construct(
        protected MessageService $messageService,
    ) {}

    public function index(Request $request): View
    {
        $user = auth()->user();
        $user->load('profile');
        $profile = $user->profile;
        $activeConversation = null;
        $otherUser = null;
        $conversationId = $request->query('conv') ? (int) $request->query('conv') : null;
        $hasBlockedOther = false;
        $isBlockedByOther = false;

        if ($conversationId) {
            $activeConversation = Conversation::find($conversationId);
            if ($activeConversation && ($activeConversation->user_a_id === $user->id || $activeConversation->user_b_id === $user->id)) {
                $otherUser = $activeConversation->otherUser($user);

                $hasBlockedOther = $this->messageService->hasBlocked($user->id, $otherUser->id);
                $isBlockedByOther = $this->messageService->isBlockedBy($user->id, $otherUser->id);

                $this->messageService->markConversationAsRead($activeConversation, $user->id);

                $activeMessages = $activeConversation->messages()->with('sender')->oldest()->get();
            } else {
                $activeMessages = collect();
                $activeConversation = null;
            }
        } else {
            $activeMessages = collect();
        }

        $conversations = $this->messageService->getUserConversations($user->id, $conversationId);

        return view('messages.index', compact('conversations', 'activeConversation', 'otherUser', 'profile', 'activeMessages', 'hasBlockedOther', 'isBlockedByOther'));
    }

    public function unreadCount(): JsonResponse
    {
        $convId = request()->query('exclude_conv') ? (int) request()->query('exclude_conv') : null;
        $count = Conversation::getUnreadGlobalCount(auth()->id(), $convId);

        return response()->json(['count' => $count]);
    }

    public function setViewing(Request $request): JsonResponse
    {
        $request->validate([
            'conversation_id' => 'required|integer|exists:conversations,id',
        ]);

        $userId = auth()->id();
        $convId = (int) $request->conversation_id;

        $conversation = Conversation::findOrFail($convId);
        $this->authorize('view', $conversation);

        $this->messageService->setViewingConversation($userId, $convId);

        return response()->json(['success' => true]);
    }

    public function clearViewing(): JsonResponse
    {
        $this->messageService->clearViewingConversation(auth()->id());

        return response()->json(['success' => true]);
    }

    public function markAsRead(Conversation $conversation): JsonResponse
    {
        $this->authorize('view', $conversation);

        $this->messageService->markConversationAsRead($conversation, auth()->id());

        return response()->json(['success' => true]);
    }

    public function markMessageAsRead(Message $message): JsonResponse
    {
        $this->authorize('view', $message);

        $marked = $this->messageService->markMessageAsRead($message, auth()->id());

        if (! $marked) {
            return response()->json(['error' => 'No puedes leer tu propio mensaje'], 400);
        }

        return response()->json(['success' => true]);
    }

    public function show(Conversation $conversation): View
    {
        $user = auth()->user();
        $profile = Profile::where('user_id', $user->id)->first();

        $this->authorize('view', $conversation);

        $this->messageService->markConversationAsRead($conversation, $user->id);

        $activeConversation = $conversation;
        $activeMessages = $conversation->messages()->with('sender')->oldest()->get();
        $otherUser = $conversation->otherUser($user);

        $hasBlockedOther = $this->messageService->hasBlocked($user->id, $otherUser->id);
        $isBlockedByOther = $this->messageService->isBlockedBy($user->id, $otherUser->id);

        $conversations = $this->messageService->getUserConversations($user->id);

        return view('messages.index', compact('activeConversation', 'otherUser', 'conversations', 'profile', 'activeMessages', 'hasBlockedOther', 'isBlockedByOther'));
    }

    public function store(Request $request, Conversation $conversation): JsonResponse
    {
        $user = auth()->user();

        $request->validate([
            'body' => 'required|string|max:2000',
        ]);

        $recipientId = $conversation->user_a_id === $user->id
            ? $conversation->user_b_id
            : $conversation->user_a_id;

        if (! $this->messageService->canSendMessage($user->id, $recipientId)) {
            return response()->json([
                'error' => 'Este usuario no acepta mensajes directos',
                'recipient_accepts_messages' => false,
            ], 403);
        }

        $message = $this->messageService->sendMessage($conversation, $user->id, $request->body);

        $this->messageService->autoReadIfViewing($message, $conversation->id, $recipientId);

        $this->messageService->broadcastNewMessage($message, $conversation->id, $recipientId);

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

        $otherUserId = (int) $request->user_id;
        $userId = auth()->id();

        if ($otherUserId === $userId) {
            return response()->json(['error' => 'No puedes enviarte mensajes a ti mismo.'], 422);
        }

        if (! $this->messageService->canSendMessage($userId, $otherUserId)) {
            return response()->json([
                'error' => 'Este usuario no acepta mensajes directos',
                'recipient_accepts_messages' => false,
            ], 403);
        }

        $conversation = $this->messageService->findOrCreateConversation($userId, $otherUserId);
        $isNewConversation = $conversation->wasRecentlyCreated;

        $message = $this->messageService->sendMessage($conversation, $userId, $request->body);

        $this->messageService->broadcastNewMessage($message, $conversation->id, $otherUserId);

        if ($isNewConversation) {
            $this->messageService->broadcastConversationCreated($conversation, $otherUserId, $message);
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
            'recipient_accepts_messages' => true,
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

        $conversation = $this->messageService->findOrCreateConversation(auth()->id(), $user->id);

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

        $conversation = $this->messageService->findOrCreateConversation(auth()->id(), $user->id);
        $isNewConversation = $conversation->wasRecentlyCreated;

        if ($isNewConversation) {
            $this->messageService->broadcastConversationCreated($conversation, $user->id);
        }

        return redirect()->route('messages.index', ['conv' => $conversation->id]);
    }

    public function blockUser(User $user): JsonResponse
    {
        $currentUser = auth()->user();

        if ($user->id === $currentUser->id) {
            return response()->json(['error' => 'No puedes bloquearte a ti mismo.'], 422);
        }

        $isBlocked = $this->messageService->hasBlocked($currentUser->id, $user->id);

        if ($isBlocked) {
            $this->messageService->unblockUser($currentUser->id, $user->id);

            broadcast(new UserBlocked($currentUser->id, $user->id, false));

            return response()->json(['blocked' => false, 'message' => 'Usuario desbloqueado.']);
        }

        $this->messageService->blockUser($currentUser->id, $user->id);

        broadcast(new UserBlocked($currentUser->id, $user->id, true));

        return response()->json(['blocked' => true, 'message' => 'Usuario bloqueado.']);
    }
}

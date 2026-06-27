<?php

declare(strict_types=1);

namespace App\Http\Controllers\Messages;

use App\Events\UserBlocked;
use App\Http\Controllers\Controller;
use App\Http\Requests\Message\SetViewingConversationRequest;
use App\Http\Requests\Message\StoreMediaMessageRequest;
use App\Http\Requests\Message\StoreMessageRequest;
use App\Http\Requests\Message\StoreNewConversationRequest;
use App\Http\Requests\Message\UpdateMessageRequest;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Project;
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
        $conversationId = $request->query('conv') ? (int) $request->query('conv') : null;

        $conversations = $this->messageService->getUserConversations($user->id, $conversationId);

        $activeConversation = null;
        $otherUser = null;
        $activeMessages = collect();
        $hasBlockedOther = false;
        $isBlockedByOther = false;

        if ($conversationId) {
            $activeConversation = $conversations->firstWhere('id', $conversationId);

            if ($activeConversation) {
                $otherUser = $activeConversation->otherChat;

                $activeConversation->unread_count = 0;

                $hasBlockedOther = $this->messageService->hasBlocked($user->id, $otherUser->id);
                $isBlockedByOther = $this->messageService->isBlockedBy($user->id, $otherUser->id);

                $this->messageService->markConversationAsRead($activeConversation, $user->id);

                $activeMessages = $activeConversation->messages()->with('sender.profile')->oldest()->get();
            }
        }

        return view('messages.index', compact('conversations', 'activeConversation', 'otherUser', 'activeMessages', 'hasBlockedOther', 'isBlockedByOther'));
    }

    public function unreadCount(): JsonResponse
    {
        $convId = request()->query('exclude_conv') ? (int) request()->query('exclude_conv') : null;
        $count = Conversation::getUnreadGlobalCount(auth()->id(), $convId);

        return response()->json(['count' => $count]);
    }

    public function setViewing(SetViewingConversationRequest $request): JsonResponse
    {
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

        $this->authorize('view', $conversation);

        $this->messageService->markConversationAsRead($conversation, $user->id);

        $activeConversation = $conversation->load(['userA', 'userB']);
        $activeMessages = $conversation->messages()->with('sender.profile')->oldest()->get();
        $otherUser = $activeConversation->otherUser($user);

        $hasBlockedOther = $this->messageService->hasBlocked($user->id, $otherUser->id);
        $isBlockedByOther = $this->messageService->isBlockedBy($user->id, $otherUser->id);

        $conversations = $this->messageService->getUserConversations($user->id);

        return view('messages.index', compact('activeConversation', 'otherUser', 'conversations', 'activeMessages', 'hasBlockedOther', 'isBlockedByOther'));
    }

    public function loadConversation(Conversation $conversation): JsonResponse
    {
        $user = auth()->user();

        $this->authorize('view', $conversation);

        $this->messageService->markConversationAsRead($conversation, $user->id);

        $otherUser = $conversation->otherUser($user);
        $activeMessages = $conversation->messages()->with('sender.profile')->oldest()->get();

        $hasBlockedOther = $this->messageService->hasBlocked($user->id, $otherUser->id);
        $isBlockedByOther = $this->messageService->isBlockedBy($user->id, $otherUser->id);

        $html = view('messages.partials.chat-panel', [
            'activeConversation' => $conversation,
            'otherUser' => $otherUser,
            'activeMessages' => $activeMessages,
            'hasBlockedOther' => $hasBlockedOther,
            'isBlockedByOther' => $isBlockedByOther,
        ])->render();

        return response()->json([
            'html' => $html,
            'conversation_id' => $conversation->id,
        ]);
    }

    public function store(StoreMessageRequest $request, Conversation $conversation): JsonResponse
    {
        return $this->sendAndRespond(
            $request,
            $conversation,
            false,
            fn($conv, $userId, $req) => $this->messageService->sendMessage($conv, $userId, $req->body)
        );
    }

    public function storeGif(StoreMediaMessageRequest $request, Conversation $conversation): JsonResponse
    {
        return $this->sendAndRespond(
            $request,
            $conversation,
            true,
            fn($conv, $userId, $req) => $this->messageService->sendGifMessage(
                $conv,
                $userId,
                $req->string('gif_url')->toString(),
                $req->string('body')->toString() ?: null,
            )
        );
    }

    public function storeMedia(StoreMediaMessageRequest $request, Conversation $conversation): JsonResponse
    {
        return $this->sendAndRespond(
            $request,
            $conversation,
            true,
            fn($conv, $userId, $req) => $this->messageService->sendMediaMessage(
                $conv,
                $userId,
                $req->file('file'),
                $req->string('media_type')->toString(),
                $req->string('body')->toString() ?: null,
            )
        );
    }

    public function userProjects(): JsonResponse
    {
        $projects = Project::where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->get(['id', 'title']);

        return response()->json($projects);
    }

    public function update(UpdateMessageRequest $request, Message $message): JsonResponse
    {
        $this->authorize('update', $message);

        $conversation = $message->conversation;
        $recipientId = $this->getRecipientId($conversation, $request->user());

        $message = $this->messageService->updateMessage($message, $request->body);
        $this->messageService->broadcastMessageEdited($message, $conversation->id, $recipientId);

        return response()->json([
            'id' => $message->id,
            'body' => $message->body,
            'edited_at' => $message->edited_at->timezone('America/Bogota')->toIso8601String(),
        ]);
    }

    public function storeNewConversation(StoreNewConversationRequest $request): JsonResponse
    {
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

    private function sendAndRespond(
        Request $request,
        Conversation $conversation,
        bool $authorize,
        callable $createMessage,
    ): JsonResponse {
        $user = $request->user();

        if ($authorize) {
            $this->authorize('view', $conversation);
        }

        $recipientId = $this->getRecipientId($conversation, $user);

        if (! $this->messageService->canSendMessage($user->id, $recipientId)) {
            return response()->json([
                'error' => 'Este usuario no acepta mensajes directos',
                'recipient_accepts_messages' => false,
            ], 403);
        }

        $message = $createMessage($conversation, $user->id, $request);

        $this->messageService->autoReadIfViewing($message, $conversation->id, $recipientId);
        $this->messageService->broadcastNewMessage($message, $conversation->id, $recipientId);

        return $this->messageResponse($message);
    }

    private function getRecipientId(Conversation $conversation, User $user): int
    {
        return $conversation->user_a_id === $user->id
            ? $conversation->user_b_id
            : $conversation->user_a_id;
    }

    private function messageResponse(Message $message): JsonResponse
    {
        $response = [
            'id' => $message->id,
            'body' => $message->body,
            'sender' => [
                'id' => $message->sender->id,
                'name' => $message->sender->name,
                'avatar_url' => $message->sender->avatar_url,
            ],
            'created_at' => $message->created_at->timezone('America/Bogota')->toIso8601String(),
            'recipient_accepts_messages' => true,
        ];

        if ($message->media_type !== null) {
            $response['media_type'] = $message->media_type;
        }
        if ($message->media_url !== null) {
            $response['media_url'] = $message->media_url;
        }
        if ($message->media_name !== null) {
            $response['media_name'] = $message->media_name;
        }
        if ($message->media_size !== null) {
            $response['media_size'] = $message->media_size;
        }

        return response()->json($response);
    }
}

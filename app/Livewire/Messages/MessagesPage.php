<?php

namespace App\Livewire\Messages;

use App\Events\NewMessageEvent;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

// ⚠️  NO uses #[Layout] aquí — tu layout usa @yield('content'), no {{ $slot }}.
//     El render() usa ->extends()->section() que sí es compatible.
class MessagesPage extends Component
{
    /* ── Estado del chat ── */
    public ?int   $activeConversationId = null;
    public array  $conversations        = [];
    public array  $messages             = [];
    public ?array $otherUser            = null;
    public string $newMessage           = '';

    /* ── Búsqueda sidebar ── */
    public string $searchQuery          = '';

    /* ── Modal nuevo mensaje ── */
    public bool   $modalOpen            = false;
    public string $modalSearch          = '';
    public array  $modalResults         = [];

    /* ── Datos del usuario autenticado (para <x-topbar>) ── */
    public $profile;

    /* ─────────────────────────────────────
       MOUNT
    ───────────────────────────────────── */
    public function mount(): void
    {
        $this->profile = Auth::user()->profile;

        $this->loadConversations();

        if ($convId = request()->get('conv')) {
            $this->selectConversation((int) $convId);
        }
    }

    /* ─────────────────────────────────────
       CARGAR CONVERSACIONES
    ───────────────────────────────────── */
    public function loadConversations(): void
    {
        $user = Auth::user();

        $this->conversations = Conversation::with(['userA.profile', 'userB.profile'])
            ->where(function ($q) use ($user) {
                $q->where('user_a_id', $user->id)
                    ->orWhere('user_b_id', $user->id);
            })
            ->where(function ($q) use ($user) {
                $q->where(function ($q2) use ($user) {
                    $q2->where('user_a_id', $user->id)->whereNull('deleted_by_a_at');
                })->orWhere(function ($q2) use ($user) {
                    $q2->where('user_b_id', $user->id)->whereNull('deleted_by_b_at');
                });
            })
            ->get()
            ->map(fn($conv) => $this->serializeConversation($conv))
            ->sortByDesc(fn($c) => $c['last_message_at'])
            ->values()
            ->all();
    }

    /* ─────────────────────────────────────
       SELECCIONAR CONVERSACIÓN
    ───────────────────────────────────── */
    public function selectConversation(int $conversationId): void
    {
        $user         = Auth::user();
        $conversation = Conversation::with([
            'userA.profile',
            'userB.profile',
            'messages.sender.profile',
        ])->find($conversationId);

        if (! $conversation) {
            return;
        }

        if ($conversation->user_a_id !== $user->id && $conversation->user_b_id !== $user->id) {
            return;
        }

        $this->activeConversationId = $conversationId;
        $this->messages = $conversation->messages
            ->map(fn($msg) => $this->serializeMessage($msg))
            ->all();

        $other           = $conversation->otherUser($user);
        $this->otherUser = $other ? $this->serializeUser($other) : null;

        Message::where('conversation_id', $conversationId)
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $this->loadConversations();
    }

    /* ─────────────────────────────────────
       ENVIAR MENSAJE
    ───────────────────────────────────── */
    public function sendMessage(): void
    {
        $body = trim($this->newMessage);

        if ($body === '' || ! $this->activeConversationId) {
            return;
        }

        $user         = Auth::user();
        $conversation = Conversation::find($this->activeConversationId);

        if (! $conversation) {
            return;
        }

        $message = $conversation->messages()->create([
            'sender_id' => $user->id,
            'body'      => $body,
        ]);

        $message->load('sender.profile');

        $this->messages[] = $this->serializeMessage($message);
        $this->newMessage  = '';

        $recipient = $conversation->user_a_id === $user->id
            ? $conversation->userB
            : $conversation->userA;

        event(new NewMessageEvent($message, $recipient));

        $this->loadConversations();
        $this->dispatch('message-sent');
    }

    /* ─────────────────────────────────────
       RECIBIR MENSAJE EN TIEMPO REAL
       (invocado desde el bridge Alpine/Echo)
    ───────────────────────────────────── */
    public function receiveMessage(array $message): void
    {
        $convId = (int) ($message['conversation_id'] ?? 0);

        if ($convId !== $this->activeConversationId) {
            $this->loadConversations();
            return;
        }

        if (! collect($this->messages)->contains('id', $message['id'])) {
            $this->messages[] = $message;
        }

        $this->loadConversations();

        Message::where('id', $message['id'])
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $this->dispatch('message-received');
    }

    /* ─────────────────────────────────────
       ELIMINAR CONVERSACIÓN
    ───────────────────────────────────── */
    public function deleteConversation(int $conversationId): void
    {
        $user         = Auth::user();
        $conversation = Conversation::find($conversationId);

        if (! $conversation) {
            return;
        }

        if ($conversation->user_a_id === $user->id) {
            $conversation->update(['deleted_by_a_at' => now()]);
        } elseif ($conversation->user_b_id === $user->id) {
            $conversation->update(['deleted_by_b_at' => now()]);
        }

        if ($this->activeConversationId === $conversationId) {
            $this->activeConversationId = null;
            $this->messages             = [];
            $this->otherUser            = null;
        }

        $this->loadConversations();
    }

    /* ─────────────────────────────────────
       BÚSQUEDA SIDEBAR
    ───────────────────────────────────── */
    public function updatedSearchQuery(): void
    {
        if (trim($this->searchQuery) === '') {
            $this->loadConversations();
            return;
        }

        $search = strtolower($this->searchQuery);
        $userId = Auth::id();

        $this->conversations = collect($this->conversations)
            ->filter(function ($conv) use ($userId, $search) {
                $other    = ($conv['user_a_id'] == $userId) ? $conv['user_b'] : $conv['user_a'];
                $name     = strtolower($other['profile']['name']     ?? '');
                $username = strtolower($other['profile']['username'] ?? '');
                return str_contains($name, $search) || str_contains($username, $search);
            })
            ->values()
            ->all();
    }

    /* ─────────────────────────────────────
       MODAL: BUSCAR USUARIOS
    ───────────────────────────────────── */
    public function openModal(): void
    {
        $this->modalOpen    = true;
        $this->modalSearch  = '';
        $this->modalResults = [];
    }

    public function closeModal(): void
    {
        $this->modalOpen    = false;
        $this->modalSearch  = '';
        $this->modalResults = [];
    }

    public function updatedModalSearch(): void
    {
        $q = trim($this->modalSearch);

        if (strlen($q) < 2) {
            $this->modalResults = [];
            return;
        }

        $this->modalResults = User::whereHas('profile', function ($query) use ($q) {
            $query->where('name', 'like', "%{$q}%")
                ->orWhere('username', 'like', "%{$q}%");
        })
            ->with('profile')
            ->where('id', '!=', Auth::id())
            ->limit(6)
            ->get()
            ->map(fn($u) => $this->serializeUser($u))
            ->all();
    }

    /* ─────────────────────────────────────
       INICIAR CONVERSACIÓN DESDE MODAL
    ───────────────────────────────────── */
    public function startConversation(int $userId): void
    {
        $currentId = Auth::id();

        $conversation = Conversation::where(function ($q) use ($currentId, $userId) {
            $q->where('user_a_id', $currentId)->where('user_b_id', $userId);
        })->orWhere(function ($q) use ($currentId, $userId) {
            $q->where('user_a_id', $userId)->where('user_b_id', $currentId);
        })->first();

        if (! $conversation) {
            $conversation = Conversation::create([
                'user_a_id' => $currentId,
                'user_b_id' => $userId,
            ]);
        }

        $this->closeModal();
        $this->selectConversation($conversation->id);
    }

    /* ─────────────────────────────────────
       RENDER
       ->extends() + ->section() = compatible con @yield('content')
    ───────────────────────────────────── */
    public function render()
    {
        return view('livewire.messages.messages-page')
            ->extends('layouts.app')
            ->section('content');
    }

    /* ─────────────────────────────────────
       HELPERS DE SERIALIZACIÓN
    ───────────────────────────────────── */
    private function serializeConversation(Conversation $conv): array
    {
        $lastMsg = $conv->messages()->latest()->first();

        return [
            'id'              => $conv->id,
            'user_a_id'       => $conv->user_a_id,
            'user_b_id'       => $conv->user_b_id,
            'user_a'          => $this->serializeUser($conv->userA),
            'user_b'          => $this->serializeUser($conv->userB),
            'last_message'          => $lastMsg?->body,
            'last_message_sender_id' => $lastMsg?->sender_id,
            'last_message_at'    => $lastMsg?->created_at?->toIso8601String()
                ?? $conv->updated_at->toIso8601String(),
            'last_message_time' => $lastMsg?->created_at
                ?->timezone('America/Bogota')
                ->format('H:i')
                ?? '',
            'unread_count'    => $conv->messages()
                ->where('sender_id', '!=', Auth::id())
                ->whereNull('read_at')
                ->count(),
        ];
    }

    private function serializeMessage(Message $msg): array
    {
        return [
            'id'              => $msg->id,
            'body'            => $msg->body,
            'sender_id'       => $msg->sender_id,
            'conversation_id' => $msg->conversation_id,
            'created_at'      => $msg->created_at->toIso8601String(),
            'read_at'         => $msg->read_at?->toIso8601String(),
            'sender'          => $msg->sender ? $this->serializeUser($msg->sender) : null,
        ];
    }

    private function serializeUser(User $user): array
    {
        $profile = $user->profile;
        $lastSeen = $profile?->last_seen_at;

        return [
            'id'         => $user->id,
            'name'       => $user->name,
            'username'   => $user->username,
            'avatar_url' => $user->avatar_url ?? asset('img/default-avatar.png'),
            'online'     => $lastSeen && $lastSeen->diffInMinutes(now()) < 5,
            'last_seen'  => $lastSeen?->timezone('America/Bogota')->diffForHumans(),
            'profile'    => [
                'name'     => $profile?->name     ?? $user->name,
                'username' => $profile?->username ?? $user->username,
                'avatar'   => $profile?->avatar   ?? null,
            ],
        ];
    }
}

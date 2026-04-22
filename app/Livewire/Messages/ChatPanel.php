<?php

namespace App\Livewire\Messages;

use App\Models\Conversation;
use App\Models\Message;
use App\Events\NewMessageEvent;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class ChatPanel extends Component
{
    public $conversation;
    public $otherUser;
    public $messages = [];
    public $newMessage = '';

    protected $listeners = ['messageReceived', 'refreshChat'];

    public function mount($conversationId = null)
    {
        if ($conversationId) {
            $this->loadConversation($conversationId);
        }
    }

    public function loadConversation($conversationId)
    {
        $user = Auth::user();
        $this->conversation = Conversation::with(['userA', 'userB', 'messages.sender'])
            ->find($conversationId);

        if ($this->conversation) {
            $this->otherUser = $this->conversation->otherUser($user);
            $this->messages = $this->conversation->messages->toArray();

            Message::where('conversation_id', $conversationId)
                ->where('sender_id', '!=', $user->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }
    }

    public function sendMessage()
    {
        if (empty(trim($this->newMessage)) || !$this->activeConversation) return;

        $user = Auth::user();
        // Recuperar el modelo real, no el array
        $conversation = Conversation::find($this->activeConversation['id']);

        $message = $conversation->messages()->create([
            'sender_id' => $user->id,
            'body' => $this->newMessage,
        ]);

        $recipient = $conversation->user_a_id === $user->id
            ? $conversation->userB
            : $conversation->userA;

        event(new NewMessageEvent($message, $recipient));
        $this->newMessage = '';
        $this->loadConversations();
    }

    public function messageReceived($message)
    {
        if ($this->conversation && $message['conversation_id'] == $this->conversation->id) {
            $this->messages[] = $message;
        }
    }

    public function refreshChat()
    {
        if ($this->conversation) {
            $this->loadConversation($this->conversation->id);
        }
    }

    public function render()
    {
        return view('livewire.messages.chat-panel');
    }
}

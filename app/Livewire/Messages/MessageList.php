<?php

namespace App\Livewire\Messages;

use App\Models\Conversation;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class MessageList extends Component
{
    public $conversations = [];
    public $activeConversationId;

    protected $listeners = ['conversationSelected', 'conversationDeleted', 'refreshList'];

    public function mount()
    {
        $this->loadConversations();
    }

    public function loadConversations()
    {
        $user = Auth::user();
        $this->conversations = Conversation::with(['userA', 'userB'])
            ->where(function ($q) use ($user) {
                $q->where('user_a_id', $user->id)->orWhere('user_b_id', $user->id);
            })
            ->where(function ($q) use ($user) {
                $q->where(function ($q2) use ($user) {
                    $q2->where('user_a_id', $user->id)
                        ->whereNull('deleted_by_a_at');
                })->orWhere(function ($q2) use ($user) {
                    $q2->where('user_b_id', $user->id)
                        ->whereNull('deleted_by_b_at');
                });
            })
            ->get()
            ->sortByDesc(fn($c) => $c->lastMessage()?->created_at)
            ->values()
            ->toArray();
    }

    public function conversationSelected($conversationId)
    {
        $this->activeConversationId = $conversationId;
    }

    public function conversationDeleted($conversationId)
    {
        $this->loadConversations();
    }

    public function refreshList()
    {
        $this->loadConversations();
    }

    public function render()
    {
        return view('livewire.messages.message-list');
    }
}

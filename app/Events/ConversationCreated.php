<?php

namespace App\Events;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Facades\Log;

class ConversationCreated implements ShouldBroadcast
{
    use InteractsWithSockets;

    public function __construct(
        public Conversation $conversation,
        public int $otherUserId,
        public ?Message $initialMessage = null
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('messages.user.' . $this->otherUserId),
            new PrivateChannel('notifications.' . $this->otherUserId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'conversation.created';
    }

    public function broadcastWith(): array
    {
        $otherUser = $this->conversation->otherUserId($this->otherUserId);

        Log::info('ConversationCreated broadcastWith', [
            'conversation_id' => $this->conversation->id,
            'otherUserId' => $this->otherUserId,
            'otherUser' => $otherUser?->name,
        ]);

        $data = [
            'id' => $this->conversation->id,
            'other_user' => [
                'id' => $otherUser->id,
                'name' => $otherUser->name,
                'avatar_url' => $otherUser->avatar_url,
            ],
            'created_at' => $this->conversation->created_at->toIso8601String(),
        ];

        if ($this->initialMessage) {
            $data['message'] = [
                'id' => $this->initialMessage->id,
                'body' => $this->initialMessage->body,
                'sender_id' => $this->initialMessage->sender_id,
                'conversation_id' => $this->conversation->id,
                'created_at' => $this->initialMessage->created_at->toIso8601String(),
                'sender' => [
                    'id' => $this->initialMessage->sender->id,
                    'name' => $this->initialMessage->sender->name,
                    'avatar_url' => $this->initialMessage->sender->avatar_url,
                ],
            ];
        }

        return $data;
    }
}
<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UserTyping implements ShouldBroadcast
{
    use InteractsWithSockets;

    public function __construct(
        public int $conversationId,
        public int $userId,
        public string $userName,
        public string $avatarUrl,
        public bool $isTyping = true
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('messages.' . $this->conversationId)];
    }

    public function broadcastAs(): string
    {
        return 'user.typing';
    }

    public function broadcastWith(): array
    {
        return [
            'conversation_id' => $this->conversationId,
            'user_id' => $this->userId,
            'user_name' => $this->userName,
            'avatar_url' => $this->avatarUrl,
            'is_typing' => $this->isTyping,
        ];
    }
}
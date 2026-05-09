<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PrivacyUpdated implements ShouldBroadcast
{
    use InteractsWithSockets;

    public function __construct(
        public int $userId,
        public string $userName,
        public bool $acceptMessages,
        public array $conversationIds
    ) {}

    public function broadcastOn(): array
    {
        $channels = array_map(
            fn($convId) => new PrivateChannel('messages.' . $convId),
            $this->conversationIds
        );

        $channels[] = new PrivateChannel('user.privacy.' . $this->userId);

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'privacy.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'user_id' => $this->userId,
            'user_name' => $this->userName,
            'accept_messages' => $this->acceptMessages,
        ];
    }
}

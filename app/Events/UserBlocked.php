<?php

declare(strict_types=1);

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UserBlocked implements ShouldBroadcast
{
    use InteractsWithSockets;

    public function __construct(
        public int $blockerId,
        public int $blockedId,
        public bool $blocked,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('messages.user.' . $this->blockedId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'user.blocked';
    }

    public function broadcastWith(): array
    {
        return [
            'blocker_id' => $this->blockerId,
            'blocked_id' => $this->blockedId,
            'blocked' => $this->blocked,
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UserBanned implements ShouldBroadcast
{
    use InteractsWithSockets;

    public function __construct(
        public int $userId,
        public string $reason,
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('user.banned.' . $this->userId)];
    }

    public function broadcastAs(): string
    {
        return 'user.banned';
    }

    public function broadcastWith(): array
    {
        return [
            'user_id' => $this->userId,
            'reason' => $this->reason,
        ];
    }
}

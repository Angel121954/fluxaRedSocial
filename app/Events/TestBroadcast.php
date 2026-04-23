<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Facades\Log;

class TestBroadcast implements ShouldBroadcast
{
    use InteractsWithSockets;

    public function __construct(public string $message) {}

    public function broadcastOn(): array
    {
        return [new Channel('public-test')];
    }

    public function broadcastWith(): array
    {
        return ['message' => $this->message];
    }
}
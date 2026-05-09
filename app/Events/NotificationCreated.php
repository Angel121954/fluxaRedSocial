<?php

namespace App\Events;

use App\Models\Notification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Facades\Log;

class NotificationCreated implements ShouldBroadcast
{
    use InteractsWithSockets;

    public function __construct(
        public Notification $notification
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('notifications.' . $this->notification->user_id)];
    }

    public function broadcastAs(): string
    {
        return 'notification.created';
    }

    public function broadcastWith(): array
    {
        $body = $this->notification->body;
        $fromUser = $this->notification->fromUser;
        if ($fromUser && str_starts_with($body, $fromUser->name)) {
            $body = trim(substr($body, strlen($fromUser->name)));
        }

        return [
            'id' => $this->notification->id,
            'type' => $this->notification->type,
            'title' => $this->notification->title,
            'body' => $body,
            'link' => $this->notification->link,
            'from_user' => $fromUser ? [
                'id' => $fromUser->id,
                'name' => $fromUser->name,
                'avatar_url' => $fromUser->avatar_url,
            ] : null,
            'created_at' => $this->notification->created_at->toIso8601String(),
        ];
    }
}
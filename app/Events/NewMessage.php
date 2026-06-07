<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NewMessage implements ShouldBroadcast
{
    use InteractsWithSockets;

    public function __construct(
        public Message $message,
        public int $conversationId,
        public int $recipientId
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('messages.' . $this->conversationId),
            new PrivateChannel('messages.user.' . $this->recipientId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function broadcastWith(): array
    {
        $data = [
            'id' => $this->message->id,
            'body' => $this->message->body,
            'sender_id' => $this->message->sender_id,
            'conversation_id' => $this->conversationId,
            'created_at' => $this->message->created_at->timezone('America/Bogota')->toIso8601String(),
            'sender' => [
                'id' => $this->message->sender->id,
                'name' => $this->message->sender->name,
                'avatar_url' => $this->message->sender->avatar_url,
            ],
        ];

        if ($this->message->isMedia()) {
            $data['media_type'] = $this->message->media_type;
            $data['media_url'] = $this->message->media_url;
            $data['media_name'] = $this->message->media_name;
        }

        return $data;
    }
}
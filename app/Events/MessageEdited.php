<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MessageEdited implements ShouldBroadcast
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
        return 'message.edited';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'body' => $this->message->body,
            'edited_at' => $this->message->edited_at?->timezone('America/Bogota')->toIso8601String(),
            'sender_id' => $this->message->sender_id,
            'conversation_id' => $this->conversationId,
        ];
    }
}

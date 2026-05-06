<?php

namespace App\Policies;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MessagePolicy
{
    public function view(User $user, Message $message): bool
    {
        return $user->id === $message->conversation->user_one_id ||
               $user->id === $message->conversation->user_two_id;
    }

    public function create(User $user, Conversation $conversation): bool
    {
        return $user->id === $conversation->user_one_id ||
               $user->id === $conversation->user_two_id;
    }

    public function delete(User $user, Message $message): bool
    {
        return $user->id === $message->user_id;
    }
}

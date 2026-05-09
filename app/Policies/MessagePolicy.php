<?php

namespace App\Policies;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MessagePolicy
{
    public function view(User $user, Message $message): bool
    {
        return $user->id === $message->conversation->user_a_id ||
               $user->id === $message->conversation->user_b_id;
    }

    public function create(User $user, Conversation $conversation): bool
    {
        if ($user->id !== $conversation->user_a_id && $user->id !== $conversation->user_b_id) {
            return false;
        }

        $otherUserId = $conversation->user_a_id === $user->id 
            ? $conversation->user_b_id 
            : $conversation->user_a_id;
        
        $otherProfile = Profile::where('user_id', $otherUserId)->first();
        
        return $otherProfile && $otherProfile->accept_messages;
    }

    public function delete(User $user, Message $message): bool
    {
        return $user->id === $message->user_id;
    }
}

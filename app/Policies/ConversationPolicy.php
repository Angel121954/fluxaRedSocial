<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ConversationPolicy
{
    public function view(User $user, Conversation $conversation): bool
    {
        return $user->id === $conversation->user_a_id || 
               $user->id === $conversation->user_b_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function sendMessage(User $user, Conversation $conversation): bool
    {
        return $user->id === $conversation->user_a_id || 
               $user->id === $conversation->user_b_id;
    }
}

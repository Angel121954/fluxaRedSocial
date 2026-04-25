<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('messages.{conversationId}', function ($user, int $conversationId) {
    return \App\Models\Conversation::where('id', $conversationId)
        ->where(function ($q) use ($user) {
            $q->where('user_a_id', $user->id)
                ->orWhere('user_b_id', $user->id);
        })
        ->exists();
});

Broadcast::channel('messages.user.{userId}', function ($user, int $userId) {
    return (int) $user->id === (int) $userId;
});

Broadcast::channel('notifications.{userId}', function ($user, int $userId) {
    return (int) $user->id === (int) $userId;
});

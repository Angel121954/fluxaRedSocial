<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    public function delete(User $user, Comment $comment): bool
    {
        return $user->id === $comment->user_id;
    }

    public function like(User $user, Comment $comment): bool
    {
        return $comment->user?->isVisible() === true
            && ! $user->isBlockedWithId($comment->user_id);
    }
}

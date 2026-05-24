<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\DiaryResponseComment;
use App\Models\User;

class DiaryResponseCommentPolicy
{
    public function delete(User $user, DiaryResponseComment $comment): bool
    {
        return $user->id === $comment->user_id;
    }
}

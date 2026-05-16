<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\Project;
use App\Models\User;

class CommentPolicy
{
    public function create(User $user, Project $project): bool
    {
        return $project->privacy === 'public' || $project->user_id === $user->id;
    }

    public function delete(User $user, Comment $comment): bool
    {
        return $user->id === $comment->user_id;
    }
}

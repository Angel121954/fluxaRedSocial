<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function view(User $user, Project $project): bool
    {
        if ($user->id === $project->user_id) {
            return true;
        }

        if ($user->isBlockedWithId($project->user_id)) {
            return false;
        }

        if ($project->privacy === 'public') {
            return true;
        }

        if ($project->privacy === 'followers' && $user->follows()->where('followed_id', $project->user_id)->exists()) {
            return true;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Project $project): bool
    {
        return $user->id === $project->user_id;
    }

    public function delete(User $user, Project $project): bool
    {
        return $user->id === $project->user_id;
    }

    public function comment(User $user, Project $project): bool
    {
        if ($project->user_id === $user->id) {
            return true;
        }

        if ($user->isBlockedWithId($project->user_id)) {
            return false;
        }

        return $project->privacy === 'public';
    }

    public function restore(User $user, Project $project): bool
    {
        return $user->id === $project->user_id;
    }

    public function forceDelete(User $user, Project $project): bool
    {
        return $user->id === $project->user_id;
    }
}

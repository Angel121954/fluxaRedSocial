<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Profile;
use App\Models\User;

class ProfilePolicy
{
    public function view(?User $user, Profile $profile): bool
    {
        return $profile->isVisibleTo($user);
    }

    public function update(User $user, Profile $profile): bool
    {
        return $user->id === $profile->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }
}

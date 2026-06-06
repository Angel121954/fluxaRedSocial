<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProfilePolicy
{
    public function view(User $user, Profile $profile): bool
    {
        if ($profile->visibility === 'public') {
            return true;
        }
        if ($profile->visibility === 'followers') {
            return $user->follows->contains($profile->user_id);
        }
        return $user->id === $profile->user_id;
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

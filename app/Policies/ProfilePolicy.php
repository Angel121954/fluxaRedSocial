<?php

namespace App\Policies;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProfilePolicy
{
    public function view(User $user, Profile $profile): bool
    {
        $fieldPrivacy = $profile->fields_privacy ?? [];
        if (empty($fieldPrivacy) || !isset($fieldPrivacy['profile']) || $fieldPrivacy['profile'] === 'public') {
            return true;
        }
        if (isset($fieldPrivacy['profile']) && $fieldPrivacy['profile'] === 'followers') {
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

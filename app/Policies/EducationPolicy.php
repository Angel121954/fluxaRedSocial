<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Education;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EducationPolicy
{
    public function view(User $user, Education $education): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Education $education): bool
    {
        return $user->id === $education->user_id;
    }

    public function delete(User $user, Education $education): bool
    {
        return $user->id === $education->user_id;
    }
}

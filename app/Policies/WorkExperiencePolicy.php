<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\WorkExperience;
use Illuminate\Auth\Access\Response;

class WorkExperiencePolicy
{
    public function view(User $user, WorkExperience $workExperience): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, WorkExperience $workExperience): bool
    {
        return $user->id === $workExperience->user_id;
    }

    public function delete(User $user, WorkExperience $workExperience): bool
    {
        return $user->id === $workExperience->user_id;
    }
}

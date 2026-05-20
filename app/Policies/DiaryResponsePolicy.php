<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\DiaryResponse;
use App\Models\User;

class DiaryResponsePolicy
{
    public function delete(User $user, DiaryResponse $response): bool
    {
        return $user->id === $response->user_id;
    }
}

<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Models\UserBlock;
use Illuminate\Support\Collection;

final class BlockService
{
    public function block(User $blocker, User $blocked): void
    {
        UserBlock::firstOrCreate([
            'blocker_id' => $blocker->id,
            'blocked_id' => $blocked->id,
        ]);

        $blocker->follows()->detach($blocked->id);
        $blocked->follows()->detach($blocker->id);
    }

    public function unblock(User $blocker, User $blocked): void
    {
        UserBlock::where('blocker_id', $blocker->id)
            ->where('blocked_id', $blocked->id)
            ->delete();
    }

    public function isBlockedBy(int $userId, int $otherUserId): bool
    {
        return UserBlock::where('blocker_id', $otherUserId)
            ->where('blocked_id', $userId)
            ->exists();
    }

    public function hasBlocked(int $blockerId, int $blockedId): bool
    {
        return UserBlock::where('blocker_id', $blockerId)
            ->where('blocked_id', $blockedId)
            ->exists();
    }

    public function blockedUsers(User $user): Collection
    {
        return $user->blockedUsers()->with('profile')->get();
    }
}

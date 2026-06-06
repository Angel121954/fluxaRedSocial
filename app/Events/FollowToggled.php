<?php

declare(strict_types=1);

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class FollowToggled implements ShouldBroadcast
{
    use InteractsWithSockets;

    public function __construct(
        public int $followerId,
        public int $targetId,
        public bool $following,
        public int $targetFollowersCount,
        public int $targetFollowingCount,
        public int $followerFollowersCount,
        public int $followerFollowingCount,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.follow.' . $this->followerId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'follow.toggled';
    }

    public function broadcastWith(): array
    {
        return [
            'follower_id' => $this->followerId,
            'target_id' => $this->targetId,
            'following' => $this->following,
            'target_followers_count' => $this->targetFollowersCount,
            'target_following_count' => $this->targetFollowingCount,
            'follower_followers_count' => $this->followerFollowersCount,
            'follower_following_count' => $this->followerFollowingCount,
        ];
    }
}

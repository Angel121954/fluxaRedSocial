<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\NotificationPreference;
use App\Models\User;
use App\Notifications\WebPushNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification as NotificationFacade;

class SendGroupedLikePush implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected int $userId,
        protected int $referenceId,
        protected string $referenceType,
        protected string $link,
    ) {}

    public function handle(): void
    {
        $key = "push.pending_likes.{$this->userId}.{$this->referenceType}.{$this->referenceId}";
        $lockKey = "push.pending_likes.lock.{$this->userId}.{$this->referenceType}.{$this->referenceId}";

        $pending = Cache::pull($key);

        if (empty($pending['names']) || ! $this->shouldSend($pending['count'])) {
            Cache::forget($lockKey);

            return;
        }

        $prefs = NotificationPreference::where('user_id', $this->userId)->first();

        if (! $prefs || ! $prefs->push_enabled || ! $prefs->notify_comments) {
            Cache::forget($lockKey);

            return;
        }

        $user = User::find($this->userId);

        if (! $user || $user->pushSubscriptions()->doesntExist()) {
            Cache::forget($lockKey);

            return;
        }

        $title = $this->referenceType === 'project' ? 'Nuevo like en tu proyecto' : 'Like en tu comentario';
        $body = $this->buildBody($pending['names'], $pending['count']);

        NotificationFacade::send($user, new WebPushNotification(
            title: $title,
            body: $body,
            link: $this->link,
        ));

        Cache::forget($lockKey);
    }

    private function shouldSend(int $totalLikes): bool
    {
        if ($totalLikes <= 0) {
            return false;
        }

        return true;
    }

    private function buildBody(array $names, int $total): string
    {
        $first = $names[0];
        $extra = $total - 1;

        if ($extra <= 0) {
            return "{$first} le gustó";
        }

        return "{$first} y a {$extra} más les gustó";
    }
}

<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Models\Comment;
use App\Models\Education;
use App\Models\Project;
use App\Models\SalaryReport;
use App\Models\User;
use App\Models\WorkExperience;
use App\Services\BadgeService;

class AwardBadgeListener
{
    public function __construct(
        protected BadgeService $badgeService,
    ) {}

    public function handle(object $event): void
    {
        $user = null;

        if ($event instanceof Project) {
            $user = $event->user;
        } elseif ($event instanceof SalaryReport) {
            $user = $event->user;
        } elseif ($event instanceof Comment) {
            $user = $event->user;
        } elseif ($event instanceof WorkExperience) {
            $user = $event->user;
        } elseif ($event instanceof Education) {
            $user = $event->user;
        } elseif (isset($event->user)) {
            $user = $event->user;
        } elseif (isset($event->user_id)) {
            $user = User::find($event->user_id);
        }

        if ($user) {
            $this->badgeService->scanUser($user);
        }
    }
}

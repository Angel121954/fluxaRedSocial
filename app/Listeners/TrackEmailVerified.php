<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Services\AnalyticsService;
use Illuminate\Auth\Events\Verified;

class TrackEmailVerified
{
    public function handle(Verified $event): void
    {
        AnalyticsService::track('email_verified', $event->user);
    }
}

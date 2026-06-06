<?php

declare(strict_types=1);

namespace App\Providers;

use App\Listeners\AwardBadgeListener;
use App\Models\Comment;
use App\Models\Education;
use App\Models\Project;
use App\Models\SalaryReport;
use App\Models\WorkExperience;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Events\CallQueuedListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    public function boot(): void
    {
        Project::created(function ($project) {
            Event::dispatch(new CallQueuedListener(
                AwardBadgeListener::class, 'handle', [$project]
            ));
        });

        SalaryReport::created(function ($report) {
            Event::dispatch(new CallQueuedListener(
                AwardBadgeListener::class, 'handle', [$report]
            ));
        });

        Comment::created(function ($comment) {
            Event::dispatch(new CallQueuedListener(
                AwardBadgeListener::class, 'handle', [$comment]
            ));
        });

        WorkExperience::created(function ($experience) {
            Event::dispatch(new CallQueuedListener(
                AwardBadgeListener::class, 'handle', [$experience]
            ));
        });

        Education::created(function ($education) {
            Event::dispatch(new CallQueuedListener(
                AwardBadgeListener::class, 'handle', [$education]
            ));
        });
    }

    public function shouldDiscoverEvents()
    {
        return false;
    }
}

<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class UserObserver
{
    public function updated(User $user): void
    {
        if ($user->wasChanged('status')) {
            $this->forgetDiscoveryCaches();
        }
    }

    public function deleted(User $user): void
    {
        $this->forgetDiscoveryCaches();
    }

    public function forceDeleted(User $user): void
    {
        $this->forgetDiscoveryCaches();
    }

    private function forgetDiscoveryCaches(): void
    {
        Cache::forget('sitemap.xml');
        Cache::forget('landing.stats');
        Cache::forget('top_technologies');
    }
}

<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Profile;
use Illuminate\Support\Facades\Cache;

class ProfileObserver
{
    public function updated(Profile $profile): void
    {
        if ($profile->wasChanged('visibility')) {
            $this->forgetDiscoveryCaches();
        }
    }

    private function forgetDiscoveryCaches(): void
    {
        Cache::forget('sitemap.xml');
        Cache::forget('landing.stats');
        Cache::forget('top_technologies');
    }
}

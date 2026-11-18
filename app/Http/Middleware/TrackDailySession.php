<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\AnalyticsService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class TrackDailySession
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null || in_array($user->role, ['guest', 'admin'], true)) {
            return $next($request);
        }

        $cacheKey = 'analytics.session.'.$user->id.'.'.sha1((string) $user->email).'.'.now()->toDateString();

        if (Cache::add($cacheKey, true, now()->endOfDay())) {
            AnalyticsService::track('session_started', $user, [], $request);
        }

        return $next($request);
    }
}

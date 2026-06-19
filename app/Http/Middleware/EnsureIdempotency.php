<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class EnsureIdempotency
{
    public function handle(Request $request, Closure $next): Response
    {
        $key = $request->header('Idempotency-Key');

        if ($key === null || $key === '') {
            return $next($request);
        }

        $cacheKey = 'idempotency:'.($request->user()?->id ?? 'guest').':'.$key;

        $cached = Cache::get($cacheKey);

        if ($cached !== null) {
            return response($cached, 200)->header('Content-Type', 'application/json');
        }

        $response = $next($request);

        if ($response->isSuccessful()) {
            Cache::put($cacheKey, $response->getContent(), now()->addHours(24));
        }

        return $response;
    }
}

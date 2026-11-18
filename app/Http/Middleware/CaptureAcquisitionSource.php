<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class CaptureAcquisitionSource
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->isMethod('GET') || ! $request->hasSession() || $request->session()->has('acquisition')) {
            return $next($request);
        }

        $source = $request->query('utm_source') ?: $request->query('ref');
        $medium = $request->query('utm_medium');
        $campaign = $request->query('utm_campaign');
        $referrer = null;

        $referrerHeader = $request->headers->get('referer');

        if (is_string($referrerHeader) && $referrerHeader !== '') {
            $referrerHost = parse_url($referrerHeader, PHP_URL_HOST);

            if (is_string($referrerHost) && $referrerHost !== '' && $referrerHost !== $request->getHost()) {
                $referrer = $referrerHost;
                $source ??= $referrerHost;
                $medium ??= 'referral';
            }
        }

        if (! $source && ! $medium && ! $campaign && ! $referrer) {
            return $next($request);
        }

        $request->session()->put('acquisition', [
            'source' => self::clean($source, 80),
            'medium' => self::clean($medium, 80),
            'campaign' => self::clean($campaign, 120),
            'referrer' => self::clean($referrer, 255),
        ]);

        return $next($request);
    }

    private static function clean(mixed $value, int $max): ?string
    {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        return Str::limit(trim($value), $max, '');
    }
}

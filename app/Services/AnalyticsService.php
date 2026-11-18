<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AnalyticsEvent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

final class AnalyticsService
{
    public static function track(
        string $name,
        ?User $user = null,
        array $properties = [],
        ?Request $request = null,
    ): void {
        try {
            if ($request === null && app()->bound('request')) {
                $request = request();
            }

            $user ??= $request?->user();

            $acquisition = [];

            if ($request?->hasSession()) {
                $acquisition = $request->session()->get('acquisition', []);
            }

            AnalyticsEvent::create([
                'user_id' => $user?->id,
                'name' => $name,
                'properties' => $properties !== [] ? $properties : null,
                'source' => self::clean($acquisition['source'] ?? $request?->query('utm_source'), 80),
                'medium' => self::clean($acquisition['medium'] ?? $request?->query('utm_medium'), 80),
                'campaign' => self::clean($acquisition['campaign'] ?? $request?->query('utm_campaign'), 120),
                'referrer' => self::clean($acquisition['referrer'] ?? null, 255),
                'path' => self::clean($request?->path(), 255),
                'created_at' => now(),
            ]);
        } catch (Throwable $e) {
            Log::warning('No se pudo registrar el evento de analítica', [
                'name' => $name,
                'user_id' => $user?->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private static function clean(mixed $value, int $max): ?string
    {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        return Str::limit(trim($value), $max, '');
    }
}

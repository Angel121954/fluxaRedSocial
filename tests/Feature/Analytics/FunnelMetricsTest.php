<?php

declare(strict_types=1);

use App\Models\AnalyticsEvent;
use App\Models\User;
use App\Services\FunnelMetricsService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Métricas del embudo', function () {
    it('cuenta como activado al usuario que actúa dentro de los 7 días', function () {
        $user = User::factory()->create();
        $registeredAt = now()->subDays(10);

        AnalyticsEvent::create([
            'user_id' => $user->id,
            'name' => 'user_registered',
            'source' => 'linkedin',
            'created_at' => $registeredAt,
        ]);

        AnalyticsEvent::create([
            'user_id' => $user->id,
            'name' => 'project_published',
            'created_at' => $registeredAt->copy()->addDay(),
        ]);

        $metrics = app(FunnelMetricsService::class);
        $from = now()->subDays(30)->startOfDay();
        $to = now()->endOfDay();

        expect($metrics->activatedUsers($from, $to))->toBe(1);

        $sources = $metrics->sources($from, $to);

        expect($sources)->toHaveCount(1)
            ->and($sources->first()['source'])->toBe('linkedin')
            ->and($sources->first()['registrations'])->toBe(1)
            ->and($sources->first()['activated'])->toBe(1);
    });

    it('no cuenta como activado al usuario que actúa después de los 7 días', function () {
        $user = User::factory()->create();
        $registeredAt = now()->subDays(20);

        AnalyticsEvent::create([
            'user_id' => $user->id,
            'name' => 'user_registered',
            'created_at' => $registeredAt,
        ]);

        AnalyticsEvent::create([
            'user_id' => $user->id,
            'name' => 'comment_created',
            'created_at' => $registeredAt->copy()->addDays(8),
        ]);

        $metrics = app(FunnelMetricsService::class);

        expect($metrics->activatedUsers(now()->subDays(30)->startOfDay(), now()->endOfDay()))->toBe(0);
    });

    it('omite el paso de visitas cuando todavía no hay page views', function () {
        $user = User::factory()->create();

        AnalyticsEvent::create([
            'user_id' => $user->id,
            'name' => 'user_registered',
            'created_at' => now()->subDay(),
        ]);

        $funnel = app(FunnelMetricsService::class)->funnel(
            now()->subDays(30)->startOfDay(),
            now()->endOfDay(),
        );

        expect($funnel[0]['key'])->toBe('user_registered');
    });

    it('elimina los eventos antiguos al podar', function () {
        AnalyticsEvent::create([
            'name' => 'page_viewed',
            'created_at' => now()->subDays(400),
        ]);

        $this->artisan('analytics:prune')->assertSuccessful();

        expect(AnalyticsEvent::count())->toBe(0);
    });

    it('calcula la retención del día 1 con la sesión del día siguiente', function () {
        $user = User::factory()->create();
        $registeredAt = now()->subDays(10);

        AnalyticsEvent::create([
            'user_id' => $user->id,
            'name' => 'user_registered',
            'created_at' => $registeredAt,
        ]);

        AnalyticsEvent::create([
            'user_id' => $user->id,
            'name' => 'session_started',
            'created_at' => $registeredAt->copy()->addDay(),
        ]);

        $retention = app(FunnelMetricsService::class)->retention(
            now()->subDays(30)->startOfDay(),
            now()->endOfDay(),
        );

        expect($retention['total'])->toBe(1)
            ->and($retention['d1'])->toBe(1)
            ->and($retention['d1_rate'])->toBe(100.0);
    });
});

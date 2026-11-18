<?php

declare(strict_types=1);

use App\Models\AnalyticsEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Panel de analítica', function () {
    it('muestra el embudo, las fuentes y la retención', function () {
        $admin = User::factory()->create([
            'role' => 'admin',
            'onboarding_completed' => true,
        ]);

        $user = User::factory()->create([
            'onboarding_completed' => true,
            'email_verified_at' => now(),
        ]);

        $registeredAt = now()->subDays(10);

        AnalyticsEvent::create([
            'name' => 'page_viewed',
            'properties' => ['page' => 'landing'],
            'source' => 'linkedin',
            'created_at' => $registeredAt->copy()->subHour(),
        ]);

        AnalyticsEvent::create([
            'user_id' => $user->id,
            'name' => 'user_registered',
            'source' => 'linkedin',
            'created_at' => $registeredAt,
        ]);

        AnalyticsEvent::create([
            'user_id' => $user->id,
            'name' => 'email_verified',
            'created_at' => $registeredAt->copy()->addMinutes(10),
        ]);

        AnalyticsEvent::create([
            'user_id' => $user->id,
            'name' => 'onboarding_completed',
            'created_at' => $registeredAt->copy()->addMinutes(20),
        ]);

        AnalyticsEvent::create([
            'user_id' => $user->id,
            'name' => 'project_published',
            'properties' => ['project_id' => 1],
            'created_at' => $registeredAt->copy()->addHour(),
        ]);

        AnalyticsEvent::create([
            'user_id' => $user->id,
            'name' => 'session_started',
            'created_at' => $registeredAt->copy()->addDay(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.analytics.index'))
            ->assertOk()
            ->assertSee('Analítica del embudo')
            ->assertSee('Embudo de activación')
            ->assertSee('Fuentes de adquisición')
            ->assertSee('linkedin')
            ->assertSee('Retención');
    });

    it('rechaza el acceso a usuarios que no son administradores', function () {
        $user = User::factory()->create(['onboarding_completed' => true]);

        $this->actingAs($user)
            ->get(route('admin.analytics.index'))
            ->assertForbidden();
    });
});

<?php

declare(strict_types=1);

use App\Models\AnalyticsEvent;
use App\Models\User;
use App\Services\ProjectService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

uses(RefreshDatabase::class);

describe('Registro de eventos de analítica', function () {
    it('registra la visita a una página pública', function () {
        $this->get('/')->assertOk();

        $this->assertDatabaseHas('analytics_events', [
            'name' => 'page_viewed',
        ]);
    });

    it('adjunta la fuente de adquisición al registro', function () {
        Notification::fake();

        $this->get('/?utm_source=linkedin&utm_medium=social&utm_campaign=launch')
            ->assertOk();

        $this->post(route('register'), [
            'name' => 'Ana Dev',
            'username' => 'anadev',
            'email' => 'anadev@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect(route('onboarding.technologies'));

        $this->assertDatabaseHas('analytics_events', [
            'name' => 'user_registered',
            'source' => 'linkedin',
            'medium' => 'social',
            'campaign' => 'launch',
        ]);
    });

    it('registra el follow y el primer proyecto', function () {
        $viewer = User::factory()->create(['onboarding_completed' => true]);
        $target = User::factory()->create(['onboarding_completed' => true]);

        $this->actingAs($viewer)
            ->postJson(route('users.follow', $target))
            ->assertOk();

        app(ProjectService::class)->create([
            'title' => 'Mi primer proyecto',
            'content' => 'Un proyecto de prueba con suficiente contenido.',
        ], $viewer->id);

        expect(AnalyticsEvent::query()->where('name', 'follow_created')->where('user_id', $viewer->id)->exists())->toBeTrue()
            ->and(AnalyticsEvent::query()->where('name', 'project_published')->where('user_id', $viewer->id)->exists())->toBeTrue();
    });

    it('registra la verificación del correo', function () {
        $user = User::factory()->unverified()->create();

        $url = URL::temporarySignedRoute('verification.verify', now()->addHour(), [
            'id' => $user->id,
            'hash' => sha1($user->email),
        ]);

        $this->actingAs($user)->get($url)->assertRedirect();

        expect(AnalyticsEvent::query()->where('name', 'email_verified')->where('user_id', $user->id)->exists())->toBeTrue();
    });

    it('registra una sola sesión diaria por usuario', function () {
        $user = User::factory()->create(['onboarding_completed' => true]);

        $this->actingAs($user)->get(route('feed.index'))->assertOk();
        $this->actingAs($user)->get(route('feed.index'))->assertOk();

        expect(AnalyticsEvent::query()
            ->where('name', 'session_started')
            ->where('user_id', $user->id)
            ->count())->toBe(1);
    });

    it('registra los pasos vistos y completados del onboarding', function () {
        $user = User::factory()->create(['onboarding_completed' => false]);

        $this->actingAs($user)->get(route('onboarding.technologies'))->assertOk();
        $this->actingAs($user)->get(route('onboarding.role'))->assertOk();

        $this->actingAs($user)
            ->post(route('onboarding.saveTechnologies'), ['technologies' => []])
            ->assertRedirect(route('onboarding.role'));

        $this->actingAs($user)
            ->post(route('onboarding.saveRole'), [
                'role' => 'backend',
                'intent' => 'grow',
            ])
            ->assertRedirect(route('onboarding.firstProject'));

        $viewed = AnalyticsEvent::query()
            ->where('name', 'onboarding_step_viewed')
            ->where('user_id', $user->id)
            ->get()
            ->map(fn (AnalyticsEvent $event): ?string => $event->properties['step'] ?? null);

        $completed = AnalyticsEvent::query()
            ->where('name', 'onboarding_step_completed')
            ->where('user_id', $user->id)
            ->get()
            ->map(fn (AnalyticsEvent $event): ?string => $event->properties['step'] ?? null);

        expect($viewed->contains('technologies'))->toBeTrue()
            ->and($viewed->contains('role'))->toBeTrue()
            ->and($completed->contains('technologies'))->toBeTrue()
            ->and($completed->contains('role'))->toBeTrue();
    });
});

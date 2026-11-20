<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function onboardingUser(): User
{
    return User::factory()->create(['onboarding_completed' => false]);
}

describe('Onboarding', function () {
    it('guarda el rol y la intención, y completa el onboarding', function () {
        $user = onboardingUser();

        $this->actingAs($user)
            ->post(route('onboarding.saveRole'), [
                'role' => 'backend',
                'intent' => 'first_job',
            ])
            ->assertRedirect(route('onboarding.firstProject'));

        $user->refresh();

        expect($user->onboarding_completed)->toBeTrue()
            ->and($user->profile->specialty)->toBe('backend')
            ->and($user->profile->intent)->toBe('first_job');
    });

    it('redirige al onboarding si intenta saltarse los pasos', function () {
        $user = onboardingUser();

        $this->actingAs($user)
            ->get(route('onboarding.firstProject'))
            ->assertRedirect(route('onboarding.technologies'));
    });

    it('muestra las opciones para publicar o importar un proyecto', function () {
        $user = User::factory()->create(['onboarding_completed' => true]);

        $this->actingAs($user)
            ->get(route('onboarding.firstProject'))
            ->assertOk()
            ->assertSee('Publica tu primer proyecto')
            ->assertSee('Crear proyecto manualmente')
            ->assertSee('Importar desde GitHub');
    });

    it('permite publicar sin verificar pero no importar desde GitHub', function () {
        $user = User::factory()->create(['onboarding_completed' => true]);
        $user->forceFill(['email_verified_at' => null])->save();

        $this->actingAs($user)
            ->get(route('onboarding.firstProject'))
            ->assertOk()
            ->assertSee('Crear proyecto manualmente')
            ->assertDontSee('Importar desde GitHub')
            ->assertSee('Verifica tu correo cuando puedas');
    });

    it('permite publicar el primer proyecto sin verificar el correo', function () {
        $user = User::factory()->create(['onboarding_completed' => true]);
        $user->forceFill(['email_verified_at' => null])->save();

        $this->actingAs($user)
            ->postJson(route('projects.store'), [
                'title' => 'Mi primer proyecto',
                'content' => 'Un proyecto de prueba con suficiente contenido.',
            ])
            ->assertCreated();

        expect($user->projects()->count())->toBe(1);
    });
});

<?php

declare(strict_types=1);

use App\Models\Profile;
use App\Models\Technology;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function onboardingUser(): User
{
    return User::factory()->create(['onboarding_completed' => false]);
}

describe('Paso final del onboarding', function () {
    it('redirige al paso de primer proyecto al terminar las sugerencias', function () {
        $user = onboardingUser();

        $this->actingAs($user)
            ->post(route('onboarding.saveSuggestions'), ['follow' => []])
            ->assertRedirect(route('onboarding.firstProject'));

        expect($user->fresh()->onboarding_completed)->toBeTrue();
    });

    it('muestra las opciones para publicar o importar un proyecto', function () {
        $user = onboardingUser();

        $this->actingAs($user)
            ->get(route('onboarding.firstProject'))
            ->assertOk()
            ->assertSee('Publica tu primer proyecto')
            ->assertSee('Crear proyecto manualmente')
            ->assertSee('Importar desde GitHub');
    });

    it('pide verificar el correo cuando el usuario aún no está verificado', function () {
        $user = onboardingUser();
        $user->forceFill(['email_verified_at' => null])->save();

        $this->actingAs($user)
            ->get(route('onboarding.firstProject'))
            ->assertOk()
            ->assertSee('Verifica tu correo para publicar')
            ->assertDontSee('Crear proyecto manualmente');
    });

    it('ofrece omitir las sugerencias sin saltarse el paso de publicar', function () {
        $user = onboardingUser();
        Profile::factory()->for($user)->create(['visibility' => 'public']);

        $this->actingAs($user)
            ->get(route('onboarding.suggestions'))
            ->assertOk()
            ->assertSee(route('onboarding.firstProject'), false)
            ->assertDontSee(route('explore.index'), false);
    });

    it('prioriza devs con tecnologías en común y excluye perfiles privados', function () {
        $viewer = onboardingUser();

        Profile::factory()->for($viewer)->create([
            'visibility' => 'public',
            'specialty' => 'backend',
        ]);

        $technology = Technology::create([
            'name' => 'Laravel',
            'slug' => 'laravel-test',
            'icon' => null,
        ]);
        $viewer->technologies()->attach($technology->id);

        $sharedDev = User::factory()->create(['name' => 'Dev Con Laravel', 'username' => 'dev-con-laravel']);
        Profile::factory()->for($sharedDev)->create(['visibility' => 'public', 'specialty' => 'frontend']);
        $sharedDev->technologies()->attach($technology->id);

        $otherDev = User::factory()->create(['name' => 'Dev Sin Stack', 'username' => 'dev-sin-stack']);
        Profile::factory()->for($otherDev)->create(['visibility' => 'public', 'specialty' => 'frontend']);

        $privateDev = User::factory()->create(['name' => 'Dev Privado', 'username' => 'dev-privado']);
        Profile::factory()->for($privateDev)->create(['visibility' => 'private', 'specialty' => 'backend']);

        $this->actingAs($viewer)
            ->get(route('onboarding.suggestions'))
            ->assertOk()
            ->assertSeeInOrder(['Dev Con Laravel', 'Dev Sin Stack'])
            ->assertDontSee('Dev Privado');
    });
});

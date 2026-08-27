<?php

declare(strict_types=1);

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class);

function landingUser(array $overrides = []): User
{
    return User::factory()->create(array_merge(['role' => 'user'], $overrides));
}

describe('Landing pública', function () {
    it('carga la landing para invitados', function () {
        $this->get('/')
            ->assertOk()
            ->assertSee('Comparte tu progreso')
            ->assertSee('Crear cuenta gratis')
            ->assertSee('Explorar como visitante');
    });

    it('muestra el estado vacío cuando no hay proyectos', function () {
        $this->get('/')
            ->assertOk()
            ->assertSee('Aún no hay proyectos publicados');
    });

    it('cachea las estadísticas globales', function () {
        landingUser();
        Project::factory()->public()->create(['user_id' => User::first()->id]);

        $this->get('/');

        expect(Cache::has('landing.stats'))->toBeTrue()
            ->and(Cache::get('landing.stats'))->toHaveKeys(['developers', 'projects', 'endorsements']);
    });

    describe('redirección según sesión', function () {
        it('redirige a explore a los usuarios invitados autenticados', function () {
            $guest = landingUser(['role' => 'guest']);

            $this->actingAs($guest)->get('/')->assertRedirect(route('explore.index'));
        });

        it('redirige al feed a los usuarios verificados', function () {
            $user = landingUser();

            $this->actingAs($user)->get('/')->assertRedirect(route('feed.index'));
        });

        it('redirige al feed a los administradores', function () {
            $admin = landingUser(['role' => 'admin']);

            $this->actingAs($admin)->get('/')->assertRedirect(route('feed.index'));
        });
    });

    describe('sección comunidad', function () {
        it('excluye proyectos privados', function () {
            $owner = landingUser();
            Project::factory()->create(['user_id' => $owner->id, 'privacy' => 'public', 'title' => 'Proyecto Visible']);
            Project::factory()->create(['user_id' => $owner->id, 'privacy' => 'private', 'title' => 'Proyecto Secreto']);

            $this->get('/')
                ->assertSee('Proyecto Visible')
                ->assertDontSee('Proyecto Secreto');
        });

        it('excluye proyectos de usuarios invitados', function () {
            $guest = landingUser(['role' => 'guest']);
            Project::factory()->create(['user_id' => $guest->id, 'privacy' => 'public', 'title' => 'De un invitado']);

            $this->get('/')->assertDontSee('De un invitado');
        });

        it('muestra el autor del proyecto público', function () {
            $owner = landingUser(['name' => 'Ana Dev']);
            Project::factory()->public()->create(['user_id' => $owner->id, 'title' => 'Mi primer proyecto']);

            $this->get('/')
                ->assertSee('Mi primer proyecto')
                ->assertSee('Ana Dev');
        });
    });
});

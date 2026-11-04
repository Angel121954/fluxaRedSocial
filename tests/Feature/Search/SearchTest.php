<?php

declare(strict_types=1);

use App\Models\Profile;
use App\Models\Project;
use App\Models\Technology;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function globalSearchViewer(): User
{
    return User::factory()->create(['onboarding_completed' => true]);
}

function globalSearchUser(string $name, string $username, array $profile = []): User
{
    $user = User::factory()->create([
        'name' => $name,
        'username' => $username,
    ]);

    Profile::factory()->for($user)->create(array_merge([
        'visibility' => 'public',
    ], $profile));

    return $user;
}

describe('Buscador global', function () {
    it('requiere autenticación', function () {
        $this->get(route('api.search', ['q' => 'laravel']))
            ->assertRedirect(route('login'));
    });

    it('encuentra usuarios públicos y proyectos por título, contenido o tecnología', function () {
        $viewer = globalSearchViewer();
        $ana = globalSearchUser('Ana Laravel', 'ana-laravel');
        globalSearchUser('Privado Laravel', 'privado-laravel', ['visibility' => 'private']);

        Project::factory()->public()->for($ana)->create(['title' => 'API Laravel']);
        Project::factory()->public()->for($ana)->create([
            'title' => 'Backend modular',
            'content' => 'Construido con Laravel y MySQL',
        ]);

        $byTechnology = Project::factory()->public()->for($ana)->create(['title' => 'Proyecto con stack']);
        $technology = Technology::create([
            'name' => 'Laravel',
            'slug' => 'laravel-search',
            'icon' => null,
        ]);
        $byTechnology->technologies()->attach($technology->id);

        $this->actingAs($viewer)
            ->getJson(route('api.search', ['q' => 'laravel']))
            ->assertOk()
            ->assertJsonFragment(['name' => 'Ana Laravel'])
            ->assertJsonFragment(['name' => 'API Laravel'])
            ->assertJsonFragment(['name' => 'Backend modular'])
            ->assertJsonFragment(['name' => 'Proyecto con stack'])
            ->assertJsonMissing(['name' => 'Privado Laravel']);
    });

    it('excluye usuarios y proyectos bloqueados o baneados', function () {
        $viewer = globalSearchViewer();
        $blocked = globalSearchUser('Bloqueado Laravel', 'bloqueado-laravel');
        $banned = User::factory()->create([
            'name' => 'Baneado Laravel',
            'username' => 'baneado-laravel',
            'status' => 'banned',
        ]);
        Profile::factory()->for($banned)->create(['visibility' => 'public']);

        $viewer->blockedUsers()->attach($blocked->id);

        Project::factory()->public()->for($blocked)->create(['title' => 'Proyecto bloqueado Laravel']);
        Project::factory()->public()->for($banned)->create(['title' => 'Proyecto baneado Laravel']);

        $this->actingAs($viewer)
            ->getJson(route('api.search', ['q' => 'laravel']))
            ->assertOk()
            ->assertJsonMissing(['name' => 'Bloqueado Laravel'])
            ->assertJsonMissing(['name' => 'Baneado Laravel'])
            ->assertJsonMissing(['name' => 'Proyecto bloqueado Laravel'])
            ->assertJsonMissing(['name' => 'Proyecto baneado Laravel']);
    });

    it('busca en contenido y tecnologías desde la página de explorar', function () {
        $viewer = globalSearchViewer();
        $author = User::factory()->create();

        Project::factory()->public()->for($author)->create([
            'title' => 'Backend modular',
            'content' => 'Construido con Laravel',
        ]);

        $byTechnology = Project::factory()->public()->for($author)->create(['title' => 'Proyecto con stack']);
        $technology = Technology::create([
            'name' => 'Laravel',
            'slug' => 'laravel-explore',
            'icon' => null,
        ]);
        $byTechnology->technologies()->attach($technology->id);

        Project::factory()->public()->for($author)->create(['title' => 'Proyecto sin coincidencia']);

        $this->actingAs($viewer)
            ->get(route('explore.search', ['q' => 'Laravel']))
            ->assertOk()
            ->assertSee('Backend modular')
            ->assertSee('Proyecto con stack')
            ->assertDontSee('Proyecto sin coincidencia');
    });
});

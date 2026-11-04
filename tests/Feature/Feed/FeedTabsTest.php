<?php

declare(strict_types=1);

use App\Models\Project;
use App\Models\Technology;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class);

function feedViewer(): User
{
    return User::factory()->create(['onboarding_completed' => true]);
}

describe('Feed', function () {
    it('muestra proyectos de la comunidad en Para ti y excluye los propios', function () {
        $viewer = feedViewer();
        $other = User::factory()->create();

        Project::factory()->public()->for($other)->create(['title' => 'Proyecto de la comunidad']);
        Project::factory()->public()->for($viewer)->create(['title' => 'Proyecto propio']);

        $this->actingAs($viewer)
            ->get(route('feed.index'))
            ->assertOk()
            ->assertSee('Proyecto de la comunidad')
            ->assertDontSee('Proyecto propio');
    });

    it('muestra solo a quienes sigues en la pestaña Siguiendo', function () {
        $viewer = feedViewer();
        $followed = User::factory()->create();
        $other = User::factory()->create();

        $viewer->follows()->attach($followed->id);

        Project::factory()->public()->for($followed)->create(['title' => 'Proyecto seguido']);
        Project::factory()->public()->for($other)->create(['title' => 'Proyecto ajeno']);

        $this->actingAs($viewer)
            ->get(route('feed.index', ['tab' => 'following']))
            ->assertOk()
            ->assertSee('Proyecto seguido')
            ->assertDontSee('Proyecto ajeno');
    });

    it('usa Para ti como respaldo cuando el usuario no sigue a nadie', function () {
        $viewer = feedViewer();
        $other = User::factory()->create();

        Project::factory()->public()->for($other)->create(['title' => 'Proyecto de respaldo']);

        $this->actingAs($viewer)
            ->get(route('feed.index', ['tab' => 'following']))
            ->assertOk()
            ->assertSee('Aún no sigues a nadie')
            ->assertSee('Proyecto de respaldo');
    });

    it('prioriza proyectos que comparten tecnologías con el usuario', function () {
        $viewer = feedViewer();
        $author = User::factory()->create();

        $technology = Technology::create([
            'name' => 'Laravel',
            'slug' => 'laravel-feed',
            'icon' => null,
        ]);
        $viewer->technologies()->attach($technology->id);

        $affine = Project::factory()->public()->for($author)->create([
            'title' => 'Proyecto afín',
            'likes_count' => 0,
            'comments_count' => 0,
        ]);
        $affine->technologies()->attach($technology->id);

        Project::factory()->public()->for($author)->create([
            'title' => 'Proyecto ajeno',
            'likes_count' => 100,
            'comments_count' => 20,
        ]);

        $this->actingAs($viewer)
            ->get(route('feed.index'))
            ->assertOk()
            ->assertSeeInOrder(['Proyecto afín', 'Proyecto ajeno']);
    });

    it('excluye proyectos de usuarios bloqueados y baneados', function () {
        $viewer = feedViewer();
        $blocked = User::factory()->create();
        $banned = User::factory()->create(['status' => 'banned']);

        $viewer->blockedUsers()->attach($blocked->id);

        Project::factory()->public()->for($blocked)->create(['title' => 'Proyecto bloqueado']);
        Project::factory()->public()->for($banned)->create(['title' => 'Proyecto baneado']);

        $this->actingAs($viewer)
            ->get(route('feed.index'))
            ->assertOk()
            ->assertDontSee('Proyecto bloqueado')
            ->assertDontSee('Proyecto baneado');
    });

    it('solo cuenta tecnologías de proyectos públicos en la barra de temas', function () {
        Cache::forget('top_technologies');

        $viewer = feedViewer();
        $author = User::factory()->create();

        $publicTechnology = Technology::create([
            'name' => 'TecnologiaPublica',
            'slug' => 'tecnologia-publica',
            'icon' => null,
        ]);
        $privateTechnology = Technology::create([
            'name' => 'TecnologiaPrivada',
            'slug' => 'tecnologia-privada',
            'icon' => null,
        ]);

        $publicProject = Project::factory()->public()->for($author)->create();
        $publicProject->technologies()->attach($publicTechnology->id);

        $privateProject = Project::factory()->private()->for($author)->create();
        $privateProject->technologies()->attach($privateTechnology->id);

        $response = $this->actingAs($viewer)->get(route('feed.index'));

        $response->assertOk()
            ->assertSee('TecnologiaPublica')
            ->assertDontSee('TecnologiaPrivada');
    });

    it('devuelve solo las tarjetas cuando se carga más desde el feed', function () {
        $viewer = feedViewer();
        $other = User::factory()->create();

        Project::factory()->public()->for($other)->create(['title' => 'Proyecto paginado']);

        $this->actingAs($viewer)
            ->get(route('feed.index'), ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk()
            ->assertSee('Proyecto paginado')
            ->assertDontSee('<!DOCTYPE html>', false);
    });
});

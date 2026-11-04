<?php

declare(strict_types=1);

use App\Models\Profile;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function publicProjectAuthor(): User
{
    $user = User::factory()->create(['name' => 'Ana Dev', 'username' => 'ana-dev']);
    Profile::factory()->for($user)->create(['visibility' => 'public']);

    return $user;
}

describe('Página pública de proyecto', function () {
    it('permite ver un proyecto público sin autenticación usando su slug', function () {
        $project = Project::factory()->public()->for(publicProjectAuthor())->create([
            'title' => 'API de tareas con Laravel',
        ]);

        $this->get(route('projects.show', $project))
            ->assertOk()
            ->assertSee('API de tareas con Laravel')
            ->assertSee('Ana Dev')
            ->assertSee('Crear cuenta gratis');
    });

    it('expone metadatos Open Graph y datos estructurados del proyecto', function () {
        $project = Project::factory()->public()->for(publicProjectAuthor())->create([
            'title' => 'API de tareas con Laravel',
        ]);

        $response = $this->get(route('projects.show', $project));

        $response->assertOk()
            ->assertSee('<meta property="og:type" content="article">', false)
            ->assertSee('<meta property="og:title" content="API de tareas con Laravel">', false)
            ->assertSee('rel="canonical"', false)
            ->assertSee('application/ld+json', false);
    });

    it('resuelve enlaces antiguos que usan el ID del proyecto', function () {
        $project = Project::factory()->public()->for(publicProjectAuthor())->create([
            'title' => 'Proyecto con enlace antiguo',
        ]);

        $this->get('/projects/'.$project->id)
            ->assertOk()
            ->assertSee('Proyecto con enlace antiguo');
    });

    it('responde 404 a visitantes en un proyecto privado', function () {
        $project = Project::factory()->private()->for(publicProjectAuthor())->create();

        $this->get(route('projects.show', $project))->assertNotFound();
    });

    it('permite al autor ver su propio proyecto privado', function () {
        $author = publicProjectAuthor();
        $project = Project::factory()->private()->for($author)->create();

        $this->actingAs($author)
            ->get(route('projects.show', $project))
            ->assertOk();
    });

    it('incluye proyectos públicos y excluye privados en el sitemap', function () {
        $author = publicProjectAuthor();
        $publicProject = Project::factory()->public()->for($author)->create();
        $privateProject = Project::factory()->private()->for($author)->create();

        $this->get(route('sitemap'))
            ->assertOk()
            ->assertSee(route('projects.show', $publicProject), false)
            ->assertDontSee(route('projects.show', $privateProject), false);
    });

    it('oculta proyectos y perfiles de usuarios baneados a los invitados', function () {
        $author = publicProjectAuthor();
        $author->forceFill(['status' => 'banned'])->save();

        $project = Project::factory()->public()->for($author)->create();

        $this->get(route('projects.show', $project))->assertNotFound();
        $this->get(route('profile.show', $author->username))->assertNotFound();
    });
});

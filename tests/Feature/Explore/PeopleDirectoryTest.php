<?php

declare(strict_types=1);

use App\Models\Profile;
use App\Models\Technology;
use App\Models\User;
use App\Services\PeopleDirectoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function peopleDirectoryViewer(array $userAttributes = []): User
{
    $viewer = User::factory()->create(array_merge([
        'onboarding_completed' => true,
        'account_type' => 'developer',
    ], $userAttributes));

    Profile::factory()->for($viewer)->create([
        'visibility' => 'public',
        'specialty' => 'backend',
        'country' => 'Colombia',
    ]);

    return $viewer;
}

function peopleDirectoryDeveloper(array $userAttributes = [], array $profileAttributes = []): User
{
    $developer = User::factory()->create(array_merge([
        'onboarding_completed' => true,
        'account_type' => 'developer',
    ], $userAttributes));

    Profile::factory()->for($developer)->create(array_merge([
        'visibility' => 'public',
    ], $profileAttributes));

    return $developer;
}

describe('Directorio de personas', function () {
    it('muestra solo desarrolladores con perfil público y visibles', function () {
        $viewer = peopleDirectoryViewer();

        peopleDirectoryDeveloper(['name' => 'Ana Pública'], ['specialty' => 'frontend', 'country' => 'Colombia']);
        peopleDirectoryDeveloper(['name' => 'Bruno Privado'], ['visibility' => 'private']);
        peopleDirectoryDeveloper(['name' => 'Baneado Total']);
        User::query()->where('name', 'Baneado Total')->update(['status' => 'banned']);

        $company = User::factory()->create([
            'name' => 'Carlos Empresa',
            'onboarding_completed' => true,
            'account_type' => 'company',
        ]);
        Profile::factory()->for($company)->create(['visibility' => 'public']);

        $blocked = peopleDirectoryDeveloper(['name' => 'Diego Bloqueado']);
        $viewer->blockedUsers()->attach($blocked->id);

        $this->actingAs($viewer)
            ->get(route('explore.people'))
            ->assertOk()
            ->assertSee('Ana Pública')
            ->assertDontSee('Bruno Privado')
            ->assertDontSee('Baneado Total')
            ->assertDontSee('Carlos Empresa')
            ->assertDontSee('Diego Bloqueado');
    });

    it('filtra desarrolladores por tecnología', function () {
        $viewer = peopleDirectoryViewer();
        $laravel = Technology::factory()->create(['name' => 'Laravel', 'slug' => 'laravel']);
        $react = Technology::factory()->create(['name' => 'React', 'slug' => 'react']);

        $laravelDev = peopleDirectoryDeveloper(['name' => 'Dev Laravel']);
        $laravelDev->technologies()->attach($laravel);

        $reactDev = peopleDirectoryDeveloper(['name' => 'Dev React']);
        $reactDev->technologies()->attach($react);

        $this->actingAs($viewer)
            ->get(route('explore.people', ['technology' => 'laravel']))
            ->assertOk()
            ->assertSee('Dev Laravel')
            ->assertDontSee('Dev React');
    });

    it('filtra desarrolladores por país y especialidad', function () {
        $viewer = peopleDirectoryViewer();

        peopleDirectoryDeveloper(
            ['name' => 'Dev Colombia'],
            ['country' => 'Colombia', 'specialty' => 'frontend'],
        );

        peopleDirectoryDeveloper(
            ['name' => 'Dev México'],
            ['country' => 'México', 'specialty' => 'backend'],
        );

        $this->actingAs($viewer)
            ->get(route('explore.people', ['country' => 'Colombia']))
            ->assertOk()
            ->assertSee('Dev Colombia')
            ->assertDontSee('Dev México');

        $this->actingAs($viewer)
            ->get(route('explore.people', ['specialty' => 'backend']))
            ->assertOk()
            ->assertSee('Dev México')
            ->assertDontSee('Dev Colombia');
    });

    it('busca desarrolladores por nombre o usuario', function () {
        $viewer = peopleDirectoryViewer();

        peopleDirectoryDeveloper(
            ['name' => 'Valentina Código', 'username' => 'valen-dev'],
        );

        peopleDirectoryDeveloper(
            ['name' => 'Otra Persona', 'username' => 'otra-persona'],
        );

        $this->actingAs($viewer)
            ->get(route('explore.people', ['q' => 'valen']))
            ->assertOk()
            ->assertSee('Valentina Código')
            ->assertDontSee('Otra Persona');
    });

    it('ordena por afinidad de stack cuando hay tecnologías compartidas', function () {
        $viewer = peopleDirectoryViewer();
        $laravel = Technology::factory()->create(['name' => 'Laravel', 'slug' => 'laravel']);
        $react = Technology::factory()->create(['name' => 'React', 'slug' => 'react']);
        $viewer->technologies()->attach($laravel);

        $match = peopleDirectoryDeveloper(['name' => 'Match Stack']);
        $match->technologies()->attach($laravel);

        $other = peopleDirectoryDeveloper(['name' => 'Otro Stack']);
        $other->technologies()->attach($react);

        $this->actingAs($viewer)
            ->get(route('explore.people'))
            ->assertOk()
            ->assertSeeInOrder(['Match Stack', 'Otro Stack']);
    });

    it('excluye al usuario que consulta el directorio', function () {
        $viewer = peopleDirectoryViewer(['name' => 'Vista Propia']);
        $other = peopleDirectoryDeveloper(['name' => 'Otro Dev']);

        $people = app(PeopleDirectoryService::class)->paginate($viewer, []);

        expect($people->pluck('id'))
            ->not->toContain($viewer->id)
            ->toContain($other->id);
    });
});

<?php

declare(strict_types=1);

use App\Models\Profile;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class);

function visibilityUser(string $visibility = 'public', string $status = 'activo'): User
{
    $user = User::factory()->create([
        'onboarding_completed' => true,
        'status' => $status,
    ]);

    Profile::factory()->for($user)->create([
        'visibility' => $visibility,
        'avatar' => null,
    ]);

    return $user->load('profile');
}

function visibilityProject(User $owner, string $privacy = 'public', string $title = 'Proyecto visible'): Project
{
    return Project::factory()->create([
        'user_id' => $owner->id,
        'privacy' => $privacy,
        'title' => $title,
    ]);
}

describe('Visibilidad del perfil y del estado en proyectos', function () {
    it('oculta los proyectos de un perfil privado a otros usuarios', function () {
        $owner = visibilityUser('private');
        $viewer = visibilityUser();
        $project = visibilityProject($owner);

        expect(Project::discoverable()->pluck('id')->all())->not->toContain($project->id);

        $this->actingAs($viewer)
            ->get(route('projects.show', $project))
            ->assertForbidden();

        $this->actingAs($owner)
            ->get(route('projects.show', $project))
            ->assertOk();
    });

    it('oculta el contenido de un usuario no visible', function (string $status) {
        $owner = visibilityUser('public', $status);
        $viewer = visibilityUser();
        $project = visibilityProject($owner);

        expect(Project::discoverable()->pluck('id')->all())->not->toContain($project->id);

        $this->actingAs($viewer)
            ->get(route('projects.show', $project))
            ->assertForbidden();
    })->with(['inactivo', 'pending_deletion']);

    it('no permite seguir ni escribir a un usuario no visible', function () {
        $viewer = visibilityUser();
        $target = visibilityUser('public', 'inactivo');

        $this->actingAs($viewer)
            ->postJson(route('users.follow', $target))
            ->assertForbidden();

        $this->actingAs($viewer)
            ->postJson(route('messages.storeNew', ['user_id' => $target->id]), [
                'user_id' => $target->id,
                'body' => 'Hola',
            ])
            ->assertForbidden();
    });

    it('invalida las cachés de descubrimiento al cambiar la visibilidad', function () {
        Cache::put('sitemap.xml', 'cache', 3600);
        Cache::put('landing.stats', ['cache'], 3600);

        $user = visibilityUser('public');
        $user->profile->update(['visibility' => 'private']);

        expect(Cache::has('sitemap.xml'))->toBeFalse()
            ->and(Cache::has('landing.stats'))->toBeFalse();
    });

    it('invalida las cachés de descubrimiento al cambiar el estado', function () {
        Cache::put('sitemap.xml', 'cache', 3600);
        Cache::put('landing.stats', ['cache'], 3600);

        $user = visibilityUser();
        $user->update(['status' => 'inactivo']);

        expect(Cache::has('sitemap.xml'))->toBeFalse()
            ->and(Cache::has('landing.stats'))->toBeFalse();
    });
});

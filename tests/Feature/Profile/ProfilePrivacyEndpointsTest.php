<?php

declare(strict_types=1);

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function privacyProfileUser(string $visibility): User
{
    $user = User::factory()->create(['onboarding_completed' => true]);
    Profile::factory()->for($user)->create(['visibility' => $visibility]);

    return $user;
}

describe('Privacidad de los endpoints de perfil', function () {
    it('oculta los proyectos de un perfil privado a otros usuarios', function () {
        $owner = privacyProfileUser('private');
        $viewer = privacyProfileUser('public');

        $this->actingAs($viewer)
            ->getJson(route('users.projects', $owner))
            ->assertForbidden();
    });

    it('oculta los seguidores de un perfil privado a otros usuarios', function () {
        $owner = privacyProfileUser('private');
        $viewer = privacyProfileUser('public');

        $this->actingAs($viewer)
            ->getJson(route('users.followers', $owner))
            ->assertForbidden();
    });

    it('oculta los seguidos de un perfil privado a otros usuarios', function () {
        $owner = privacyProfileUser('private');
        $viewer = privacyProfileUser('public');

        $this->actingAs($viewer)
            ->getJson(route('users.following', $owner))
            ->assertForbidden();
    });

    it('permite al dueño consultar sus propios endpoints privados', function () {
        $owner = privacyProfileUser('private');

        $this->actingAs($owner)
            ->getJson(route('users.projects', $owner))
            ->assertOk()
            ->assertJsonStructure(['projects']);

        $this->actingAs($owner)
            ->getJson(route('users.followers', $owner))
            ->assertOk()
            ->assertJsonStructure(['followers']);

        $this->actingAs($owner)
            ->getJson(route('users.following', $owner))
            ->assertOk()
            ->assertJsonStructure(['following']);
    });

    it('permite consultar los endpoints de un perfil público', function () {
        $owner = privacyProfileUser('public');
        $viewer = privacyProfileUser('public');

        $this->actingAs($viewer)
            ->getJson(route('users.projects', $owner))
            ->assertOk();
    });
});

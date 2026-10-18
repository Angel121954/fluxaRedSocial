<?php

declare(strict_types=1);

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function publicProfileUser(array $profile = []): User
{
    $user = User::factory()->create(['role' => 'user']);
    Profile::factory()->for($user)->create($profile);

    return $user;
}

describe('Perfil público', function () {
    it('permite ver un perfil público sin autenticación', function () {
        $user = publicProfileUser(['visibility' => 'public', 'bio' => 'Hola comunidad']);

        $this->get(route('profile.show', $user->username))
            ->assertOk()
            ->assertSee($user->name)
            ->assertSee('Iniciar sesión');
    });

    it('responde 404 a visitantes en un perfil privado', function () {
        $user = publicProfileUser(['visibility' => 'private']);

        $this->get(route('profile.show', $user->username))->assertNotFound();
    });

    it('responde 404 a visitantes en un perfil solo para seguidores', function () {
        $user = publicProfileUser(['visibility' => 'followers']);

        $this->get(route('profile.show', $user->username))->assertNotFound();
    });

    it('permite al dueño ver su perfil privado cuando está autenticado', function () {
        $user = publicProfileUser(['visibility' => 'private']);

        $this->actingAs($user)
            ->get(route('profile.show', $user->username))
            ->assertOk();
    });
});

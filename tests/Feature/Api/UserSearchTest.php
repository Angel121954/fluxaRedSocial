<?php

declare(strict_types=1);

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Búsqueda de usuarios', function () {
    it('encuentra usuarios públicos por nombre y username', function () {
        $public = User::factory()->create([
            'name' => 'Buscable Publico',
            'username' => 'buscable_publico',
        ]);
        Profile::factory()->for($public)->create(['visibility' => 'public']);

        $private = User::factory()->create([
            'name' => 'Buscable Privado',
            'username' => 'buscable_privado',
        ]);
        Profile::factory()->for($private)->create(['visibility' => 'private']);

        $response = $this->getJson('/api/search?q=Buscable');

        $response->assertOk();

        $usernames = collect($response->json('users'))->pluck('username');

        expect($usernames)->toContain('buscable_publico')
            ->not->toContain('buscable_privado');
    });

    it('busca por username sin generar errores de columnas', function () {
        $user = User::factory()->create(['username' => 'busqueda_por_username']);
        Profile::factory()->for($user)->create(['visibility' => 'public']);

        $this->getJson('/api/search?q=busqueda_por_username')
            ->assertOk()
            ->assertJsonFragment([
                'name' => $user->name,
                'username' => 'busqueda_por_username',
            ]);
    });
});

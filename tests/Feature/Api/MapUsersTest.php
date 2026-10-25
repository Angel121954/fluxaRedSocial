<?php

declare(strict_types=1);

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function mapUser(array $userAttributes = [], array $profileAttributes = []): User
{
    $user = User::factory()->create(array_merge([
        'onboarding_completed' => true,
        'status' => 'activo',
        'role' => 'user',
    ], $userAttributes));

    Profile::factory()->for($user)->create(array_merge([
        'visibility' => 'public',
        'latitude' => 4.6097,
        'longitude' => -74.0817,
    ], $profileAttributes));

    return $user;
}

describe('Mapa de usuarios', function () {
    it('solo incluye perfiles públicos, activos y sin bloqueos', function () {
        $viewer = mapUser([], ['latitude' => null, 'longitude' => null]);
        $visible = mapUser();
        $private = mapUser([], ['visibility' => 'private']);
        $banned = mapUser(['status' => 'banned']);
        $guest = mapUser(['role' => 'guest', 'status' => 'temporal']);
        $blocked = mapUser();
        $blocker = mapUser();

        $viewer->blockedUsers()->attach($blocked->id);
        $blocker->blockedUsers()->attach($viewer->id);

        $response = $this->actingAs($viewer)
            ->getJson(route('explore.map.users'))
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment(['username' => $visible->username]);

        $response->assertJsonMissing(['username' => $private->username]);
        $response->assertJsonMissing(['username' => $banned->username]);
        $response->assertJsonMissing(['username' => $guest->username]);
        $response->assertJsonMissing(['username' => $blocked->username]);
        $response->assertJsonMissing(['username' => $blocker->username]);
    });
});

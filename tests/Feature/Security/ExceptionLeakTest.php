<?php

declare(strict_types=1);

use App\Models\Profile;
use App\Models\User;
use App\Services\GitHubService;
use App\Services\ProfileService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;

uses(RefreshDatabase::class);

describe('Fugas de detalles internos', function () {
    it('no expone el mensaje interno al fallar la importación de GitHub', function () {
        $this->mock(GitHubService::class, function ($mock): void {
            $mock->shouldReceive('importRepo')
                ->once()
                ->andThrow(new RuntimeException('token secreto de GitHub'));
        });

        $user = User::factory()->create([
            'onboarding_completed' => true,
            'github_token' => 'token-test',
        ]);

        $this->actingAs($user)
            ->postJson(route('github.repos.import'), [
                'full_name' => 'owner/repo',
            ])
            ->assertStatus(500)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'No se pudo importar el repositorio. Intenta de nuevo.')
            ->assertJsonMissing(['message' => 'token secreto de GitHub']);
    });

    it('no expone el mensaje interno al fallar la actualización del avatar', function () {
        $this->mock(ProfileService::class, function ($mock): void {
            $mock->shouldReceive('updateAvatar')
                ->once()
                ->andThrow(new RuntimeException('detalle interno de Cloudinary'));
        });

        $user = User::factory()->create(['onboarding_completed' => true]);
        Profile::factory()->for($user)->create();

        $this->actingAs($user)
            ->post(route('profile.avatar'), [
                'avatar' => UploadedFile::fake()->image('avatar.jpg'),
            ], ['Accept' => 'application/json'])
            ->assertStatus(500)
            ->assertJsonPath('message', 'No se pudo actualizar la foto de perfil. Intenta de nuevo.')
            ->assertJsonMissing(['message' => 'detalle interno de Cloudinary']);
    });

    it('no expone el mensaje interno al fallar la eliminación del avatar', function () {
        $this->mock(ProfileService::class, function ($mock): void {
            $mock->shouldReceive('deleteAvatar')
                ->once()
                ->andThrow(new RuntimeException('detalle interno de Cloudinary'));
        });

        $user = User::factory()->create(['onboarding_completed' => true]);
        Profile::factory()->for($user)->create([
            'avatar' => 'https://example.com/avatar.png',
        ]);

        $this->actingAs($user)
            ->deleteJson(route('profile.avatar.destroy'))
            ->assertStatus(500)
            ->assertJsonPath('message', 'No se pudo eliminar la foto de perfil. Intenta de nuevo.')
            ->assertJsonMissing(['message' => 'detalle interno de Cloudinary']);
    });
});

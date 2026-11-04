<?php

declare(strict_types=1);

use App\Models\Profile;
use App\Models\User;
use App\Services\CVService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function cvDownloadUser(string $visibility = 'public', array $cvSettings = []): User
{
    $user = User::factory()->create(['onboarding_completed' => true]);

    Profile::factory()->for($user)->create([
        'visibility' => $visibility,
        'cv_settings' => $cvSettings,
    ]);

    return $user->load('profile');
}

describe('Privacidad de descarga del CV', function () {
    it('bloquea la descarga a otros usuarios cuando allow_download está desactivado', function () {
        $owner = cvDownloadUser('public', ['allow_download' => false]);
        $viewer = cvDownloadUser();

        $this->mock(CVService::class, function ($mock): void {
            $mock->shouldReceive('settingsFor')
                ->once()
                ->andReturn(['allow_download' => false, 'format' => 'pdf']);
            $mock->shouldNotReceive('generateAtsPdf');
        });

        $this->actingAs($viewer)
            ->get(route('cv.download.public.format', [
                'username' => $owner->username,
                'format' => 'ats',
            ]))
            ->assertForbidden();
    });

    it('permite la descarga a otros usuarios cuando allow_download está activo', function () {
        $owner = cvDownloadUser('public', ['allow_download' => true]);
        $viewer = cvDownloadUser();

        $this->mock(CVService::class, function ($mock): void {
            $mock->shouldReceive('settingsFor')
                ->once()
                ->andReturn(['allow_download' => true, 'format' => 'pdf']);
            $mock->shouldReceive('generateAtsPdf')
                ->once()
                ->andReturn('pdf-bytes');
        });

        $response = $this->actingAs($viewer)
            ->get(route('cv.download.public.format', [
                'username' => $owner->username,
                'format' => 'ats',
            ]));

        $response->assertOk();

        expect($response->getContent())->toBe('pdf-bytes');
    });

    it('permite al dueño descargar su CV aunque allow_download esté desactivado', function () {
        $owner = cvDownloadUser('public', ['allow_download' => false]);

        $this->mock(CVService::class, function ($mock): void {
            $mock->shouldReceive('settingsFor')
                ->once()
                ->andReturn(['allow_download' => false, 'format' => 'pdf']);
            $mock->shouldReceive('generateAtsPdf')
                ->once()
                ->andReturn('pdf-bytes');
        });

        $response = $this->actingAs($owner)
            ->get(route('cv.download.public.format', [
                'username' => $owner->username,
                'format' => 'ats',
            ]));

        $response->assertOk();

        expect($response->getContent())->toBe('pdf-bytes');
    });

    it('mantiene el bloqueo por visibilidad del perfil aunque allow_download esté activo', function () {
        $owner = cvDownloadUser('private', ['allow_download' => true]);
        $viewer = cvDownloadUser();

        $this->mock(CVService::class, function ($mock): void {
            $mock->shouldNotReceive('settingsFor');
            $mock->shouldNotReceive('generateAtsPdf');
        });

        $this->actingAs($viewer)
            ->get(route('cv.download.public.format', [
                'username' => $owner->username,
                'format' => 'ats',
            ]))
            ->assertForbidden();
    });

    it('guarda allow_download en la configuración del CV', function () {
        $user = cvDownloadUser();

        $this->actingAs($user)
            ->put(route('cv.update'), [
                'format' => 'pdf',
                'allow_download' => '0',
            ])
            ->assertRedirect();

        expect($user->profile->fresh()->cv_settings['allow_download'])->toBeFalse();
    });
});

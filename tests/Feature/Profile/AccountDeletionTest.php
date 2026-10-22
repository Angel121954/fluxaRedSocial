<?php

declare(strict_types=1);

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Eliminación de cuenta', function () {
    it('programa la eliminación de la cuenta dentro de 30 días', function () {
        $user = User::factory()->create(['onboarding_completed' => true]);
        Profile::factory()->for($user)->create();

        $this->actingAs($user)
            ->delete(route('account.destroy'))
            ->assertRedirect(route('login'));

        $user->refresh();

        expect($user->status)->toBe('pending_deletion')
            ->and($user->delete_at)->not->toBeNull()
            ->and($user->delete_at->isFuture())->toBeTrue();
    });

    it('elimina definitivamente las cuentas vencidas', function () {
        config()->set('cloudinary.cloud_url', 'cloudinary://key:secret@test');

        $user = User::factory()->create([
            'status' => 'pending_deletion',
            'delete_at' => now()->subDay(),
        ]);
        Profile::factory()->for($user)->create(['avatar' => null]);

        $this->artisan('users:delete-expired')->assertSuccessful();

        expect(User::withTrashed()->find($user->id))->toBeNull();
    });
});

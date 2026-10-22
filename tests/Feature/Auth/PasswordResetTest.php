<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

uses(RefreshDatabase::class);

describe('Recuperación de contraseña', function () {
    it('guarda el token al solicitar el enlace de restablecimiento', function () {
        Notification::fake();

        $user = User::factory()->create();

        $this->post(route('password.email'), ['email' => $user->email])
            ->assertSessionHasNoErrors();

        expect(DB::table('password_reset_tokens')->where('email', $user->email)->exists())->toBeTrue();
    });

    it('restablece la contraseña con un token válido', function () {
        $user = User::factory()->create();
        $token = Password::broker()->createToken($user);

        $this->post(route('password.store'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'NuevaPassword123',
            'password_confirmation' => 'NuevaPassword123',
        ])->assertRedirect(route('login'));

        expect(Hash::check('NuevaPassword123', $user->fresh()->password))->toBeTrue();
    });
});

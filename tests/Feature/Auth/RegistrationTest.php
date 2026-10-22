<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

describe('Registro de usuarios', function () {
    it('crea el usuario con perfil y preferencias desde el registro propio', function () {
        Notification::fake();

        $username = 'registro_'.Str::lower(Str::random(8));

        $this->post('/register', [
            'name' => 'Ana Registro',
            'username' => $username,
            'email' => $username.'@gmail.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ])->assertRedirect(route('onboarding.accountType'));

        $user = User::where('username', $username)->first();

        expect($user)->not->toBeNull()
            ->and($user->role)->toBe('user')
            ->and($user->profile)->not->toBeNull()
            ->and($user->notificationPreferences)->not->toBeNull();
    });
});

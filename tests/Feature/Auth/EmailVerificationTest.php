<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Verificación de correo', function () {
    it('redirige a verificar el correo cuando la cuenta no está verificada', function () {
        $user = User::factory()->unverified()->create(['onboarding_completed' => true]);

        $this->actingAs($user)
            ->get(route('messages.index'))
            ->assertRedirect(route('verification.notice'));
    });

    it('permite el acceso cuando el correo está verificado', function () {
        $user = User::factory()->create(['onboarding_completed' => true]);

        $this->actingAs($user)
            ->get(route('messages.index'))
            ->assertOk();
    });

    it('muestra la pantalla para verificar el correo', function () {
        $user = User::factory()->unverified()->create(['onboarding_completed' => true]);

        $this->actingAs($user)
            ->get(route('verification.notice'))
            ->assertOk()
            ->assertSee('Verifica tu correo electrónico');
    });

    it('marca a los invitados como verificados para no bloquear su sesión', function () {
        $this->post(route('auth.guest'));

        $guest = User::where('role', 'guest')->first();

        expect($guest)->not->toBeNull()
            ->and($guest->email_verified_at)->not->toBeNull();
    });

    it('no permite iniciar sesión como invitado por GET', function () {
        $this->get(route('auth.guest'))->assertMethodNotAllowed();

        expect(User::where('role', 'guest')->count())->toBe(0);
    });
});

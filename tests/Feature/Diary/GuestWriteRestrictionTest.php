<?php

declare(strict_types=1);

use App\Models\Diary;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Escrituras del diario', function () {
    it('bloquea a los invitados al publicar una respuesta', function () {
        $guest = User::factory()->create([
            'role' => 'guest',
            'onboarding_completed' => true,
        ]);

        $this->actingAs($guest)
            ->postJson(route('diary.reply'), ['content' => 'Respuesta de invitado'])
            ->assertForbidden();

        expect($guest->fresh()->role)->toBe('guest');
    });

    it('permite publicar a un usuario registrado', function () {
        $diary = Diary::create([
            'question' => '¿Qué estás construyendo?',
            'status' => 'active',
        ]);

        $user = User::factory()->create(['onboarding_completed' => true]);
        Profile::factory()->for($user)->create();

        $this->actingAs($user)
            ->postJson(route('diary.reply'), ['content' => 'Mi proyecto'])
            ->assertOk();

        expect($diary->responses()->where('user_id', $user->id)->exists())->toBeTrue();
    });
});

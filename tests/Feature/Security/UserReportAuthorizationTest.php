<?php

declare(strict_types=1);

use App\Models\Profile;
use App\Models\User;
use App\Models\UserBlock;
use App\Models\UserReport;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function reportSecurityUser(string $visibility = 'public'): User
{
    $user = User::factory()->create(['onboarding_completed' => true]);

    Profile::factory()->for($user)->create([
        'visibility' => $visibility,
        'accept_messages' => true,
        'avatar' => null,
    ]);

    return $user->load('profile');
}

describe('Autorización para reportar usuarios', function () {
    it('impide reportar un perfil privado', function () {
        $reporter = reportSecurityUser();
        $target = reportSecurityUser('private');

        $this->actingAs($reporter)
            ->postJson(route('users.report', $target), [
                'reason' => 'Motivo suficientemente largo para el reporte',
            ])
            ->assertForbidden();

        expect(UserReport::count())->toBe(0);
    });

    it('impide reportar a un usuario bloqueado', function () {
        $reporter = reportSecurityUser();
        $target = reportSecurityUser();

        UserBlock::create([
            'blocker_id' => $target->id,
            'blocked_id' => $reporter->id,
        ]);

        $this->actingAs($reporter)
            ->postJson(route('users.report', $target), [
                'reason' => 'Motivo suficientemente largo para el reporte',
            ])
            ->assertForbidden();

        expect(UserReport::count())->toBe(0);
    });

    it('permite reportar un perfil público', function () {
        $reporter = reportSecurityUser();
        $target = reportSecurityUser();

        $this->actingAs($reporter)
            ->postJson(route('users.report', $target), [
                'reason' => 'Motivo suficientemente largo para el reporte',
            ])
            ->assertOk();

        expect(UserReport::where('reporter_id', $reporter->id)
            ->where('reported_id', $target->id)
            ->exists())->toBeTrue();
    });

    it('mantiene el rechazo al autorreporte', function () {
        $user = reportSecurityUser();

        $this->actingAs($user)
            ->postJson(route('users.report', $user), [
                'reason' => 'Motivo suficientemente largo para el reporte',
            ])
            ->assertStatus(400);

        expect(UserReport::count())->toBe(0);
    });
});

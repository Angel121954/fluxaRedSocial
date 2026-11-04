<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\UserBlock;
use App\Services\BlockService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function blockServiceUser(): User
{
    return User::factory()->create(['onboarding_completed' => true]);
}

describe('BlockService', function () {
    it('bloquea a un usuario y desvincula los follows en ambos sentidos', function () {
        $blocker = blockServiceUser();
        $blocked = blockServiceUser();

        $blocker->follows()->attach($blocked->id);
        $blocked->follows()->attach($blocker->id);

        (new BlockService)->block($blocker, $blocked);

        expect(UserBlock::where('blocker_id', $blocker->id)
            ->where('blocked_id', $blocked->id)
            ->exists())->toBeTrue()
            ->and($blocker->follows()->where('followed_id', $blocked->id)->exists())->toBeFalse()
            ->and($blocked->follows()->where('followed_id', $blocker->id)->exists())->toBeFalse();
    });

    it('desbloquea a un usuario', function () {
        $blocker = blockServiceUser();
        $blocked = blockServiceUser();

        $service = new BlockService;
        $service->block($blocker, $blocked);
        $service->unblock($blocker, $blocked);

        expect($service->hasBlocked($blocker->id, $blocked->id))->toBeFalse()
            ->and($service->isBlockedBy($blocked->id, $blocker->id))->toBeFalse();
    });

    it('detecta bloqueos en la dirección correcta', function () {
        $blocker = blockServiceUser();
        $blocked = blockServiceUser();

        (new BlockService)->block($blocker, $blocked);

        expect((new BlockService)->hasBlocked($blocker->id, $blocked->id))->toBeTrue()
            ->and((new BlockService)->isBlockedBy($blocked->id, $blocker->id))->toBeTrue()
            ->and((new BlockService)->hasBlocked($blocked->id, $blocker->id))->toBeFalse()
            ->and((new BlockService)->isBlockedBy($blocker->id, $blocked->id))->toBeFalse();
    });

    it('devuelve la lista de usuarios bloqueados', function () {
        $blocker = blockServiceUser();
        $blocked = blockServiceUser();

        (new BlockService)->block($blocker, $blocked);

        $blockedUsers = (new BlockService)->blockedUsers($blocker);

        expect($blockedUsers)->toHaveCount(1)
            ->and($blockedUsers->first()->id)->toBe($blocked->id);
    });
});

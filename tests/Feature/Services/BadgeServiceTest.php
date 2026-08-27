<?php

declare(strict_types=1);

use App\Models\Badge;
use App\Models\Notification;
use App\Models\Profile;
use App\Models\Project;
use App\Models\User;
use App\Services\BadgeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

function badgeMake(string $name, array $config, string $category = 'proyectos'): Badge
{
    return Badge::create([
        'name' => $name,
        'slug' => Str::slug($name).'-'.Str::random(4),
        'description' => 'Logro de prueba',
        'icon' => 'star',
        'category' => $category,
        'criteria_type' => 'count_check',
        'criteria_config' => $config,
        'tier' => 1,
        'order' => 1,
    ]);
}

function badgeSvc(): BadgeService
{
    return new BadgeService;
}

describe('BadgeService', function () {
    describe('userMeetsCriteria', function () {
        it('compara conteos según el operador', function () {
            $user = User::factory()->create();
            $badge = badgeMake('Tres proyectos', ['model' => 'projects', 'operator' => '>=', 'value' => 2]);

            expect(badgeSvc()->userMeetsCriteria($user, $badge))->toBeFalse();

            Project::factory()->count(2)->create(['user_id' => $user->id]);

            expect(badgeSvc()->userMeetsCriteria($user, $badge))->toBeTrue();
        });

        it('calcula la completitud del perfil', function () {
            $user = User::factory()->create();
            $badge = badgeMake('Perfil completo', ['model' => 'profile_completeness', 'operator' => '>=', 'value' => 20], 'perfil');

            expect(badgeSvc()->userMeetsCriteria($user, $badge))->toBeFalse();

            Profile::factory()->create(['user_id' => $user->id, 'bio' => 'Hola, soy dev']);

            $user->unsetRelation('profile');

            expect(badgeSvc()->userMeetsCriteria($user, $badge))->toBeTrue();
        });

        it('retorna false para criterios sin modelo conocido', function () {
            $user = User::factory()->create();
            $badge = badgeMake('Raro', ['model' => 'huevos_habiles', 'operator' => '>=', 'value' => 1]);

            expect(badgeSvc()->userMeetsCriteria($user, $badge))->toBeFalse();
        });
    });

    describe('scanUser', function () {
        it('otorga la insignia cuando el usuario cumple el criterio', function () {
            Event::fake();
            $user = User::factory()->create();
            badgeMake('Creador', ['model' => 'projects', 'operator' => '>=', 'value' => 2]);
            Project::factory()->count(2)->create(['user_id' => $user->id]);

            badgeSvc()->scanUser($user);

            expect($user->badges)->toHaveCount(1)
                ->and(Notification::count())->toBe(1)
                ->and(Notification::first()->type)->toBe('badge');
        });

        it('no otorga la insignia si no cumple el criterio', function () {
            $user = User::factory()->create();
            badgeMake('Creador', ['model' => 'projects', 'operator' => '>=', 'value' => 3]);
            Project::factory()->count(2)->create(['user_id' => $user->id]);

            badgeSvc()->scanUser($user);

            expect($user->badges)->toHaveCount(0);
        });

        it('no duplica insignias ya otorgadas', function () {
            Event::fake();
            $user = User::factory()->create();
            $badge = badgeMake('Creador', ['model' => 'projects', 'operator' => '>=', 'value' => 1]);

            badgeSvc()->awardBadge($user, $badge);
            badgeSvc()->scanUser($user);

            expect($user->badges()->count())->toBe(1);
        });
    });

    describe('scanAll', function () {
        it('otorga insignias solo a los usuarios que aplican', function () {
            Event::fake();
            $winner = User::factory()->create();
            $loser = User::factory()->create();
            badgeMake('Creador', ['model' => 'projects', 'operator' => '>=', 'value' => 1]);
            Project::factory()->create(['user_id' => $winner->id]);

            $awarded = badgeSvc()->scanAll();

            expect($awarded)->toBe(1)
                ->and($winner->badges()->count())->toBe(1)
                ->and($loser->badges()->count())->toBe(0);
        });
    });
});

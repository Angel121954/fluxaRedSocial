<?php

declare(strict_types=1);

use App\Models\Profile;
use App\Models\Project;
use App\Models\Technology;
use App\Models\User;
use App\Services\CloudinaryService;
use App\Services\ProfileService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Mockery as m;

uses(RefreshDatabase::class);

function profService(?CloudinaryService $cloudinary = null): ProfileService
{
    return new ProfileService($cloudinary ?? m::mock(CloudinaryService::class));
}

function profUser(): User
{
    return User::factory()->create();
}

describe('ProfileService', function () {
    describe('updateAvatar', function () {
        it('actualiza el avatar del perfil existente', function () {
            $cloudinary = m::mock(CloudinaryService::class);
            $cloudinary->shouldReceive('uploadAvatar')
                ->once()
                ->andReturn(['secure_url' => 'https://res.cloudinary.com/avatar-1.jpg']);

            $user = profUser();
            Profile::factory()->create(['user_id' => $user->id]);

            $url = (new ProfileService($cloudinary))->updateAvatar($user->id, UploadedFile::fake()->image('avatar.jpg'));

            expect($url)->toBe('https://res.cloudinary.com/avatar-1.jpg')
                ->and(Profile::where('user_id', $user->id)->first()->avatar)->toBe($url);
        });

        it('crea el perfil si el usuario no tiene uno', function () {
            $cloudinary = m::mock(CloudinaryService::class);
            $cloudinary->shouldReceive('uploadAvatar')
                ->once()
                ->andReturn(['secure_url' => 'https://res.cloudinary.com/avatar-2.jpg']);

            $user = profUser();

            (new ProfileService($cloudinary))->updateAvatar($user->id, UploadedFile::fake()->image('avatar.jpg'));

            expect(Profile::count())->toBe(1)
                ->and(Profile::first()->user_id)->toBe($user->id)
                ->and(Profile::first()->avatar)->toBe('https://res.cloudinary.com/avatar-2.jpg');
        });
    });

    describe('deleteAvatar', function () {
        it('retorna false cuando el usuario no tiene avatar', function () {
            $user = profUser();

            expect(profService()->deleteAvatar($user->id))->toBeFalse();
        });

        it('elimina el avatar de Cloudinary y resetea el campo', function () {
            $cloudinary = m::mock(CloudinaryService::class);
            $cloudinary->shouldReceive('delete')->once()->with('fluxa/avatares/user_1')->andReturn(true);

            $user = profUser();
            Profile::factory()->create([
                'user_id' => $user->id,
                'avatar' => 'https://res.cloudinary.com/demo/image/upload/v123456/fluxa/avatares/user_1.jpg',
            ]);

            $result = (new ProfileService($cloudinary))->deleteAvatar($user->id);

            expect($result)->toBeTrue()
                ->and(Profile::first()->avatar)->toBeNull();
        });
    });

    describe('loadProfileData', function () {
        it('oculta los proyectos privados a otros usuarios', function () {
            $owner = profUser();
            Project::factory()->public()->create(['user_id' => $owner->id, 'title' => 'Publico']);
            Project::factory()->private()->create(['user_id' => $owner->id, 'title' => 'Privado']);

            $viewer = profUser();
            $data = profService()->loadProfileData($owner, $viewer);

            expect($data['projects']->pluck('title')->values()->all())->toBe(['Publico']);
        });

        it('muestra todos los proyectos al dueño del perfil', function () {
            $owner = profUser();
            Project::factory()->public()->create(['user_id' => $owner->id, 'title' => 'Publico', 'created_at' => now()->subHour()]);
            Project::factory()->private()->create(['user_id' => $owner->id, 'title' => 'Privado', 'created_at' => now()]);

            $data = profService()->loadProfileData($owner, $owner);

            expect($data['projects']->pluck('title')->values()->all())->toBe(['Privado', 'Publico']);
        });

        it('agrupa las tecnologías por categoría', function () {
            $user = profUser();
            $php = Technology::create(['name' => 'PHP', 'slug' => 'php', 'icon' => 'php', 'category' => 'language']);
            $user->technologies()->attach($php->id);

            $data = profService()->loadProfileData($user, $user);

            expect($data['groupedTechnologies'])->toHaveKey('language')
                ->and($data['groupedTechnologies']['language']->pluck('name'))->toContain('PHP');
        });

        it('coloca las tecnologías favoritas primero dentro de su categoría', function () {
            $user = profUser();
            $lang = 'language';
            $alpha = Technology::create(['name' => 'Alpha', 'slug' => 'alpha', 'icon' => 'alpha', 'category' => $lang]);
            $favorita = Technology::create(['name' => 'Beta', 'slug' => 'beta', 'icon' => 'beta', 'category' => $lang]);
            $gamma = Technology::create(['name' => 'Gamma', 'slug' => 'gamma', 'icon' => 'gamma', 'category' => $lang]);

            $user->technologies()->attach([$alpha->id, $gamma->id]);
            $user->technologies()->attach($favorita->id, ['is_favorite' => true]);

            $data = profService()->loadProfileData($user, $user);

            expect($data['groupedTechnologies'][$lang]->pluck('name')->values()->all())
                ->toBe(['Beta', 'Alpha', 'Gamma']);
        });
    });

    describe('getTimeline', function () {
        it('ordena las actividades de más reciente a más antigua', function () {
            $older = Project::factory()->create(['created_at' => now()->subDays(2)]);
            $newer = Project::factory()->create(['created_at' => now()->subDay()]);

            $timeline = profService()->getTimeline(
                collect([$older, $newer]),
                collect(),
                collect(),
                collect(),
            );

            expect($timeline->first()['type'])->toBe('project')
                ->and($timeline->first()['date']->toDateTimeString())->toBe($newer->created_at->toDateTimeString());
        });
    });
});

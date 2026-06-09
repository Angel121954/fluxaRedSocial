<?php

declare(strict_types=1);

use App\Models\Notification;
use App\Models\Profile;
use App\Models\Project;
use App\Models\ProjectBookmark;
use App\Models\ProjectLike;
use App\Models\ProjectMedia;
use App\Models\SkillEndorsement;
use App\Models\Technology;
use App\Models\User;
use App\Services\CloudinaryService;
use App\Services\ProjectService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;

uses(RefreshDatabase::class);

function projCreateUser(array $userOverrides = []): User
{
    $user = User::factory()->create($userOverrides);
    Profile::factory()->create(['user_id' => $user->id]);

    return $user;
}

function projService(?CloudinaryService $cloudinary = null): ProjectService
{
    return new ProjectService($cloudinary ?? Mockery::mock(CloudinaryService::class));
}

function createTechnology(string $name): Technology
{
    return Technology::factory()->create(['name' => $name]);
}

describe('ProjectService', function () {
    describe('create', function () {
        it('crea un proyecto correctamente', function () {
            $user = projCreateUser();

            $project = projService()->create([
                'title' => 'Mi Proyecto',
                'content' => 'Descripcion del proyecto',
                'privacy' => 'public',
            ], $user->id);

            expect($project)->toBeInstanceOf(Project::class)
                ->and($project->title)->toBe('Mi Proyecto')
                ->and($project->content)->toBe('Descripcion del proyecto')
                ->and($project->privacy)->toBe('public')
                ->and($project->user_id)->toBe($user->id)
                ->and(Project::count())->toBe(1);
        });

        it('usa public como privacidad por defecto', function () {
            $user = projCreateUser();

            $project = projService()->create([
                'title' => 'Sin privacidad',
                'content' => 'Test',
            ], $user->id);

            expect($project->privacy)->toBe('public');
        });

        it('asocia tecnologias cuando se pasan', function () {
            $user = projCreateUser();
            $php = createTechnology('PHP');
            $laravel = createTechnology('Laravel');

            $project = projService()->create([
                'title' => 'Con techs',
                'content' => 'Test',
                'techs' => ['PHP', 'Laravel'],
            ], $user->id);

            expect($project->technologies->pluck('id')->toArray())
                ->toBe([$php->id, $laravel->id]);
        });

        it('crea proyecto sin tecnologias', function () {
            $user = projCreateUser();

            $project = projService()->create([
                'title' => 'Sin techs',
                'content' => 'Test',
            ], $user->id);

            expect($project->technologies)->toBeEmpty();
        });
    });

    describe('update', function () {
        it('actualiza los campos del proyecto', function () {
            $user = projCreateUser();
            $project = Project::factory()->create(['user_id' => $user->id]);

            $updated = projService()->update($project, [
                'title' => 'Nuevo titulo',
                'content' => 'Nuevo contenido',
                'privacy' => 'private',
            ]);

            expect($updated->title)->toBe('Nuevo titulo')
                ->and($updated->content)->toBe('Nuevo contenido')
                ->and($updated->privacy)->toBe('private');
        });

        it('resincroniza tecnologias cuando se pasan', function () {
            $user = projCreateUser();
            $php = createTechnology('PHP');
            $js = createTechnology('JavaScript');
            $project = Project::factory()->create(['user_id' => $user->id]);
            $project->technologies()->attach($php->id);

            projService()->update($project, [
                'title' => 'Updated',
                'content' => 'Content',
                'techs' => ['JavaScript'],
            ]);

            expect($project->fresh()->technologies->pluck('id')->toArray())
                ->toBe([$js->id])
                ->and($project->fresh()->technologies)->not->toContain($php);
        });
    });

    describe('delete', function () {
        it('elimina el proyecto con soft delete', function () {
            $user = projCreateUser();
            $project = Project::factory()->create(['user_id' => $user->id]);

            projService()->delete($project);

            expect(Project::count())->toBe(0)
                ->and(Project::withTrashed()->count())->toBe(1);
        });

        it('elimina los archivos de Cloudinary si el proyecto tiene media', function () {
            $user = projCreateUser();
            $project = Project::factory()->create(['user_id' => $user->id]);
            $media = ProjectMedia::create([
                'project_id' => $project->id,
                'media_url' => 'https://cloudinary.com/img.jpg',
                'public_id' => 'projects/img1',
                'type' => 'image',
                'position' => 0,
            ]);

            $cloudinary = Mockery::mock(CloudinaryService::class);
            $cloudinary->shouldReceive('delete')
                ->once()
                ->with('projects/img1', 'image')
                ->andReturn(true);

            (new ProjectService($cloudinary))->delete($project);

            expect(ProjectMedia::count())->toBe(0);
        });
    });

    describe('toggleLike', function () {
        it('agrega un like cuando no existe', function () {
            $owner = projCreateUser();
            $liker = projCreateUser();
            $project = Project::factory()->create(['user_id' => $owner->id, 'likes_count' => 0]);

            Event::fake();

            $result = projService()->toggleLike($project, $liker->id);

            expect($result['is_liked'])->toBeTrue()
                ->and($result['likes_count'])->toBe(1)
                ->and(ProjectLike::count())->toBe(1)
                ->and($project->fresh()->likes_count)->toBe(1);
        });

        it('quita el like cuando ya existe', function () {
            $owner = projCreateUser();
            $liker = projCreateUser();
            $project = Project::factory()->create(['user_id' => $owner->id, 'likes_count' => 1]);
            ProjectLike::create(['user_id' => $liker->id, 'project_id' => $project->id]);

            $result = projService()->toggleLike($project, $liker->id);

            expect($result['is_liked'])->toBeFalse()
                ->and($result['likes_count'])->toBe(0)
                ->and(ProjectLike::count())->toBe(0)
                ->and($project->fresh()->likes_count)->toBe(0);
        });

        it('crea notificacion cuando un usuario diferente da like', function () {
            $owner = projCreateUser();
            $liker = projCreateUser();
            $project = Project::factory()->create(['user_id' => $owner->id]);

            projService()->toggleLike($project, $liker->id);

            expect(Notification::count())->toBe(1)
                ->and(Notification::first()->user_id)->toBe($owner->id)
                ->and(Notification::first()->type)->toBe('like');
        });

        it('no crea notificacion cuando el dueño se da like a si mismo', function () {
            $user = projCreateUser();
            $project = Project::factory()->create(['user_id' => $user->id]);

            projService()->toggleLike($project, $user->id);

            expect(Notification::count())->toBe(0);
        });
    });

    describe('toggleBookmark', function () {
        it('agrega un bookmark cuando no existe', function () {
            $user = projCreateUser();
            $project = Project::factory()->create(['user_id' => $user->id]);

            $result = projService()->toggleBookmark($project, $user->id);

            expect($result['is_bookmarked'])->toBeTrue()
                ->and(ProjectBookmark::count())->toBe(1);
        });

        it('quita el bookmark cuando ya existe', function () {
            $user = projCreateUser();
            $project = Project::factory()->create(['user_id' => $user->id]);
            ProjectBookmark::create(['user_id' => $user->id, 'project_id' => $project->id]);

            $result = projService()->toggleBookmark($project, $user->id);

            expect($result['is_bookmarked'])->toBeFalse()
                ->and(ProjectBookmark::count())->toBe(0);
        });
    });

    describe('endorseProject', function () {
        it('agrega un endorsement', function () {
            $owner = projCreateUser();
            $endorser = projCreateUser();
            $project = Project::factory()->create(['user_id' => $owner->id]);

            $result = projService()->endorseProject($project, $endorser->id, 'collaboration');

            expect($result['is_endorsed'])->toBeTrue()
                ->and($result['user_endorsement'])->toBe('collaboration')
                ->and(SkillEndorsement::count())->toBe(1);
        });

        it('lanza excepcion al endorsar tu propio proyecto', function () {
            $user = projCreateUser();
            $project = Project::factory()->create(['user_id' => $user->id]);

            expect(fn () => projService()->endorseProject($project, $user->id, 'leadership'))
                ->toThrow(\Exception::class, 'No puedes recomendar las habilidades de tu propio proyecto.');
        });

        it('cambia el skill type si ya existe un endorsement', function () {
            $owner = projCreateUser();
            $endorser = projCreateUser();
            $project = Project::factory()->create(['user_id' => $owner->id]);
            SkillEndorsement::create([
                'user_id' => $endorser->id,
                'project_id' => $project->id,
                'skill_type' => 'collaboration',
            ]);

            $result = projService()->endorseProject($project, $endorser->id, 'leadership');

            expect($result['is_endorsed'])->toBeTrue()
                ->and($result['user_endorsement'])->toBe('leadership')
                ->and(SkillEndorsement::count())->toBe(1);
        });

        it('elimina el endorsement si es el mismo skill type', function () {
            $owner = projCreateUser();
            $endorser = projCreateUser();
            $project = Project::factory()->create(['user_id' => $owner->id]);
            SkillEndorsement::create([
                'user_id' => $endorser->id,
                'project_id' => $project->id,
                'skill_type' => 'architecture',
            ]);

            $result = projService()->endorseProject($project, $endorser->id, 'architecture');

            expect($result['is_endorsed'])->toBeFalse()
                ->and($result['user_endorsement'])->toBeNull()
                ->and(SkillEndorsement::count())->toBe(0);
        });

        it('crea notificacion al endorsar', function () {
            $owner = projCreateUser();
            $endorser = projCreateUser();
            $project = Project::factory()->create(['user_id' => $owner->id]);

            projService()->endorseProject($project, $endorser->id, 'logical_thinking');

            expect(Notification::count())->toBe(1)
                ->and(Notification::first()->type)->toBe('endorsement')
                ->and(Notification::first()->user_id)->toBe($owner->id);
        });

        it('retorna skill counts correctos', function () {
            $owner = projCreateUser();
            $endorser = projCreateUser();
            $project = Project::factory()->create(['user_id' => $owner->id]);

            $result = projService()->endorseProject($project, $endorser->id, 'leadership');

            expect($result['skill_counts'])->toHaveKeys([
                'technical_communication', 'logical_thinking',
                'collaboration', 'architecture', 'leadership',
            ]);
        });
    });

    describe('attachMedia', function () {
        it('sube archivos a Cloudinary y los asocia al proyecto', function () {
            $user = projCreateUser();
            $project = Project::factory()->create(['user_id' => $user->id]);

            $cloudinary = Mockery::mock(CloudinaryService::class);
            $cloudinary->shouldReceive('uploadProjectMedia')
                ->twice()
                ->andReturn(
                    ['secure_url' => 'https://cloudinary.com/img1.jpg', 'public_id' => 'projects/img1'],
                    ['secure_url' => 'https://cloudinary.com/img2.jpg', 'public_id' => 'projects/img2'],
                );

            $files = [
                UploadedFile::fake()->image('foto1.jpg'),
                UploadedFile::fake()->image('foto2.jpg'),
            ];

            (new ProjectService($cloudinary))->attachMedia($project, $files);

            expect(ProjectMedia::count())->toBe(2)
                ->and($project->media->first()->position)->toBe(0)
                ->and($project->media->last()->position)->toBe(1);
        });
    });
});

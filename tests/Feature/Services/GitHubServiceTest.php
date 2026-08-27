<?php

declare(strict_types=1);

use App\Models\Project;
use App\Models\ProjectMedia;
use App\Models\Technology;
use App\Models\User;
use App\Services\CloudinaryService;
use App\Services\GitHubService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Mockery as m;

uses(RefreshDatabase::class);

function ghService(?CloudinaryService $cloudinary = null): GitHubService
{
    return new GitHubService($cloudinary ?? m::mock(CloudinaryService::class));
}

function ghCloudinaryMock(): CloudinaryService
{
    $cloudinary = m::mock(CloudinaryService::class);
    $cloudinary->shouldReceive('upload')
        ->once()
        ->andReturn([
            'secure_url' => 'https://res.cloudinary.com/fluxa/preview.png',
            'public_id' => 'fluxa/proyectos/preview',
        ]);

    return $cloudinary;
}

function ghUser(): User
{
    return User::factory()->create(['github_token' => 'token-test']);
}

describe('GitHubService', function () {
    describe('getRepos', function () {
        it('retorna la lista de repositorios mapeada', function () {
            Http::fake([
                'api.github.com/user/repos*' => Http::response([[
                    'id' => 1,
                    'name' => 'fluxa',
                    'full_name' => 'fluxa/fluxa',
                    'description' => 'Red social',
                    'language' => 'PHP',
                    'html_url' => 'https://github.com/fluxa/fluxa',
                    'private' => false,
                    'topics' => ['laravel'],
                    'default_branch' => 'main',
                    'updated_at' => '2026-01-01T00:00:00Z',
                ]]),
            ]);

            $repos = ghService()->getRepos(ghUser());

            expect($repos)->toHaveCount(1)
                ->and($repos[0])->toMatchArray([
                    'id' => 1,
                    'name' => 'fluxa',
                    'full_name' => 'fluxa/fluxa',
                    'language' => 'PHP',
                    'private' => false,
                    'default_branch' => 'main',
                ]);
        });

        it('retorna arreglo vacío si la API falla', function () {
            Http::fake(['api.github.com/*' => Http::response([], 401)]);

            expect(ghService()->getRepos(ghUser()))->toBe([]);
        });
    });

    describe('importRepo', function () {
        it('crea un proyecto y asocia las tecnologías conocidas', function () {
            Technology::create(['name' => 'PHP', 'slug' => 'php', 'icon' => 'php']);
            Http::fake([
                'api.github.com/repos/fluxa/fluxa' => Http::response([
                    'name' => 'fluxa',
                    'full_name' => 'fluxa/fluxa',
                    'description' => 'Red social de devs',
                    'html_url' => 'https://github.com/fluxa/fluxa',
                    'private' => false,
                    'language' => 'PHP',
                    'topics' => ['laravel', 'docker'],
                ]),
                'opengraph.githubassets.com/*' => Http::response('imagen-binaria'),
            ]);

            $user = ghUser();
            $result = ghService(ghCloudinaryMock())->importRepo($user, 'fluxa/fluxa');

            expect($result['project'])->toBeInstanceOf(Project::class)
                ->and($result['project']->privacy)->toBe('public')
                ->and($result['project']->user_id)->toBe($user->id)
                ->and($result['project']->content)->toContain('Repositorio')
                ->and($result['project']->technologies->pluck('slug'))->toContain('php')
                ->and($result['skipped_techs'])->toContain('laravel')
                ->and($result['skipped_techs'])->toContain('docker')
                ->and(ProjectMedia::count())->toBe(1);
        });

        it('marca el proyecto como privado cuando el repo es privado', function () {
            Http::fake([
                'api.github.com/repos/fluxa/privado' => Http::response([
                    'name' => 'privado',
                    'full_name' => 'fluxa/privado',
                    'description' => null,
                    'html_url' => 'https://github.com/fluxa/privado',
                    'private' => true,
                    'language' => null,
                    'topics' => [],
                ]),
            ]);

            $result = ghService()->importRepo(ghUser(), 'fluxa/privado');

            expect($result['project']->privacy)->toBe('private')
                ->and($result['project']->content)->toContain('importado desde GitHub');
        });

        it('lanza una excepción si no se puede obtener el repositorio', function () {
            Http::fake(['api.github.com/*' => Http::response([], 404)]);

            expect(fn () => ghService()->importRepo(ghUser(), 'fluxa/inexistente'))
                ->toThrow(RuntimeException::class);
        });
    });

    describe('disconnect', function () {
        it('limpia los datos de conexión de GitHub', function () {
            Http::fake(['api.github.com/*' => Http::response([], 204)]);
            $user = ghUser();

            ghService()->disconnect($user);

            expect($user->fresh()->github_token)->toBeNull()
                ->and($user->fresh()->github_refresh_token)->toBeNull()
                ->and($user->fresh()->github_token_expires_at)->toBeNull();
        });
    });
});

<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Project;
use App\Models\Technology;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GitHubService
{
    protected const API_URL = 'https://api.github.com';

    public function __construct(
        protected CloudinaryService $cloudinaryService,
    ) {}

    public function getRepos(User $user): array
    {
        $response = Http::withToken($user->github_token)
            ->accept('application/vnd.github.v3+json')
            ->get(self::API_URL . '/user/repos', [
                'per_page' => 100,
                'sort' => 'updated',
                'type' => 'all',
            ]);

        if ($response->failed()) {
            Log::error('GitHub API error listing repos', [
                'user_id' => $user->id,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [];
        }

        return array_map(fn ($repo) => [
            'id' => $repo['id'],
            'name' => $repo['name'],
            'full_name' => $repo['full_name'],
            'description' => $repo['description'] ?? '',
            'language' => $repo['language'],
            'html_url' => $repo['html_url'],
            'private' => $repo['private'],
            'topics' => $repo['topics'] ?? [],
            'default_branch' => $repo['default_branch'],
            'social_preview' => "https://opengraph.githubassets.com/1/{$repo['full_name']}",
            'updated_at' => $repo['updated_at'],
        ], $response->json());
    }

    public function importRepo(User $user, string $fullName): array
    {
        $response = Http::withToken($user->github_token)
            ->accept('application/vnd.github.v3+json')
            ->get(self::API_URL . '/repos/' . $fullName);

        if ($response->failed()) {
            throw new \RuntimeException('No se pudo obtener información del repositorio: ' . $fullName);
        }

        $repo = $response->json();

        $title = $repo['name'];
        $description = $repo['description']
            ?? $repo['name']
            . ' — Proyecto importado desde GitHub';

        $content = $description;
        if ($repo['html_url']) {
            $content .= "\n\n🔗 Repositorio: " . $repo['html_url'];
        }

        $project = Project::create([
            'user_id' => $user->id,
            'title' => $title,
            'content' => $content,
            'privacy' => $repo['private'] ? 'private' : 'public',
        ]);

        $techNames = array_filter([
            $repo['language'],
            ...($repo['topics'] ?? []),
        ]);

        $result = $this->syncTechnologies($project, $techNames);

        $this->attachSocialPreview($project, $repo['full_name']);

        Log::info('Proyecto importado desde GitHub', [
            'project_id' => $project->id,
            'repo' => $fullName,
            'user_id' => $user->id,
        ]);

        return ['project' => $project, 'skipped_techs' => $result['skipped']];
    }

    protected function attachSocialPreview(Project $project, string $fullName): void
    {
        $previewUrl = "https://opengraph.githubassets.com/1/{$fullName}";

        try {
            $imageContent = Http::withOptions(['timeout' => 10])
                ->get($previewUrl)
                ->body();

            if (empty($imageContent)) {
                return;
            }

            $tempPath = tempnam(sys_get_temp_dir(), 'gh_preview_');
            file_put_contents($tempPath, $imageContent);

            $uploaded = $this->cloudinaryService->upload(
                new \Illuminate\Http\UploadedFile($tempPath, 'preview.png', 'image/png', null, true),
                'fluxa/proyectos',
                null,
                ['resource_type' => 'image']
            );

            $project->media()->create([
                'media_url' => $uploaded['secure_url'],
                'public_id' => $uploaded['public_id'],
                'type' => 'image',
                'position' => 0,
            ]);

            unlink($tempPath);
        } catch (\Exception $e) {
            Log::warning('No se pudo adjuntar social preview de GitHub', [
                'project_id' => $project->id,
                'repo' => $fullName,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function disconnect(User $user): void
    {
        $user->update([
            'github_token' => null,
            'github_refresh_token' => null,
            'github_token_expires_at' => null,
        ]);

        try {
            Http::withToken($user->github_token)
                ->accept('application/vnd.github.v3+json')
                ->delete(self::API_URL . '/applications/' . config('services.github.client_id') . '/grant');
        } catch (\Exception $e) {
            Log::warning('No se pudo revocar token de GitHub', ['user_id' => $user->id]);
        }
    }

    protected function syncTechnologies(Project $project, array $techs): array
    {
        $skipped = [];

        if (empty($techs)) {
            return ['skipped' => $skipped];
        }

        $lowerSlugs = array_map(fn ($t) => strtolower(trim($t)), $techs);

        $matched = Technology::whereIn('name', $techs)
            ->orWhereIn('slug', $lowerSlugs)
            ->get(['id', 'name', 'slug']);

        $techIds = $matched->pluck('id')->unique();

        foreach ($techs as $tech) {
            $normalized = strtolower(trim($tech));

            $found = $matched->contains(function ($t) use ($tech, $normalized) {
                return $t->name === $tech
                    || $t->slug === $normalized
                    || strtolower($t->name) === $normalized;
            });

            if (! $found) {
                $skipped[] = $tech;
            }
        }

        foreach ($techIds as $id) {
            try {
                $project->technologies()->attach($id);
            } catch (\Exception $e) {
                Log::warning('No se pudo adjuntar tecnología al proyecto', [
                    'project_id' => $project->id,
                    'technology_id' => $id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return ['skipped' => $skipped];
    }
}

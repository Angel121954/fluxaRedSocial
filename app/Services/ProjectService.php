<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectBookmark;
use App\Models\ProjectLike;
use App\Models\ProjectMedia;
use App\Models\SkillEndorsement;
use App\Models\Technology;
use App\Models\User;
use App\Notifications\CreatesNotifications;
use Illuminate\Support\Facades\Log;

class ProjectService
{
    protected CloudinaryService $cloudinaryService;

    public function __construct(CloudinaryService $cloudinaryService)
    {
        $this->cloudinaryService = $cloudinaryService;
    }

    public function create(array $data, int $userId): Project
    {
        Log::info('Creando proyecto', [
            'user_id' => $userId,
            'title' => $data['title'],
        ]);

        $project = Project::create([
            'user_id' => $userId,
            'title' => $data['title'],
            'content' => $data['content'],
            'privacy' => $data['privacy'] ?? 'public',
        ]);

        Log::info('Proyecto creado', [
            'project_id' => $project->id,
            'user_id' => $project->user_id,
        ]);

        if (! empty($data['techs'])) {
            $this->syncTechnologies($project, $data['techs']);
        }

        return $project;
    }

    public function update(Project $project, array $data): Project
    {
        Log::info('Actualizando proyecto', [
            'project_id' => $project->id,
            'title' => $data['title'],
        ]);

        $project->update([
            'title' => $data['title'],
            'content' => $data['content'],
            'privacy' => $data['privacy'] ?? $project->privacy,
        ]);

        $techIds = ! empty($data['techs'])
            ? Technology::whereIn('name', $data['techs'])->pluck('id')
            : collect();

        $project->technologies()->sync($techIds);

        return $project->fresh();
    }

    public function delete(Project $project): void
    {
        foreach ($project->media as $media) {
            if (! empty($media->public_id)) {
                $resourceType = $media->type === 'video' ? 'video' : 'image';
                $this->cloudinaryService->delete($media->public_id, $resourceType);
            }
        }

        $project->delete();

        Log::info('Proyecto eliminado', ['project_id' => $project->id]);
    }

    public function toggleLike(Project $project, int $userId): array
    {
        $existingLike = ProjectLike::where('user_id', $userId)
            ->where('project_id', $project->id)
            ->first();

        if ($existingLike) {
            $existingLike->delete();
            $project->decrement('likes_count');
            $isLiked = false;
        } else {
            ProjectLike::create([
                'user_id' => $userId,
                'project_id' => $project->id,
            ]);
            $project->increment('likes_count');
            $isLiked = true;

            if ($project->user_id !== $userId) {
                CreatesNotifications::notifyProjectLike(
                    $project->user_id,
                    $userId,
                    User::find($userId)->name,
                    $project->id,
                    $project->title
                );
            }
        }

        return [
            'likes_count' => $project->likes_count,
            'is_liked' => $isLiked,
        ];
    }

    public function toggleBookmark(Project $project, int $userId): array
    {
        $existingBookmark = ProjectBookmark::where('user_id', $userId)
            ->where('project_id', $project->id)
            ->first();

        if ($existingBookmark) {
            $existingBookmark->delete();
            $isBookmarked = false;
        } else {
            ProjectBookmark::create([
                'user_id' => $userId,
                'project_id' => $project->id,
            ]);
            $isBookmarked = true;
        }

        return [
            'is_bookmarked' => $isBookmarked,
        ];
    }

    public function endorseProject(Project $project, int $userId, string $skillType): array
    {
        if ($project->user_id === $userId) {
            throw new \Exception('No puedes recomendar las habilidades de tu propio proyecto.');
        }

        $currentEndorsement = SkillEndorsement::where('user_id', $userId)
            ->where('project_id', $project->id)
            ->first();

        $isNewEndorsement = false;
        $isEndorsed = false;
        $userEndorsement = null;

        if ($currentEndorsement) {
            if ($currentEndorsement->skill_type === $skillType) {
                $currentEndorsement->delete();
                $isEndorsed = false;
                $userEndorsement = null;
            } else {
                $currentEndorsement->delete();
                SkillEndorsement::create([
                    'user_id' => $userId,
                    'project_id' => $project->id,
                    'skill_type' => $skillType,
                ]);
                $isEndorsed = true;
                $userEndorsement = $skillType;
                $isNewEndorsement = true;
            }
        } else {
            SkillEndorsement::create([
                'user_id' => $userId,
                'project_id' => $project->id,
                'skill_type' => $skillType,
            ]);
            $isEndorsed = true;
            $userEndorsement = $skillType;
            $isNewEndorsement = true;
        }

        if ($isNewEndorsement && $project->user_id !== $userId) {
            CreatesNotifications::notifyEndorsement(
                $project->user_id,
                $userId,
                User::find($userId)->name,
                $project->id,
                $project->title,
                $skillType
            );
        }

        $skillCounts = SkillEndorsement::getSkillCounts($project->id);
        $dbUserEndorsement = SkillEndorsement::getUserEndorsement($userId, $project->id);

        return [
            'skill_counts' => $skillCounts,
            'user_endorsement' => $dbUserEndorsement,
            'is_endorsed' => $isEndorsed,
        ];
    }

    public function deleteProjectMedia(ProjectMedia $media): void
    {
        if ($media->public_id) {
            $resourceType = $media->type === 'video' ? 'video' : 'image';
            $this->cloudinaryService->delete($media->public_id, $resourceType);
        }

        $media->delete();

        Log::info('Media eliminada del proyecto', [
            'media_id' => $media->id,
            'project_id' => $media->project_id,
            'public_id' => $media->public_id,
        ]);
    }

    public function attachMedia(Project $project, array $files): void
    {
        foreach ($files as $position => $file) {
            $uploaded = $this->cloudinaryService->uploadProjectMedia($file, $position);

            $mime = $file->getMimeType();
            $mediaType = match (true) {
                str_starts_with($mime, 'video/') => 'video',
                $mime === 'image/gif' => 'gif',
                default => 'image',
            };

            $project->media()->create([
                'media_url' => $uploaded['secure_url'],
                'public_id' => $uploaded['public_id'],
                'type' => $mediaType,
                'position' => $position,
            ]);

            Log::info('Media adjuntada al proyecto', [
                'project_id' => $project->id,
                'media_type' => $mediaType,
                'position' => $position,
            ]);
        }
    }

    protected function syncTechnologies(Project $project, array $techs): void
    {
        $techIds = Technology::whereIn('name', $techs)->pluck('id');
        $project->technologies()->sync($techIds);

        Log::info('Tecnologías sincronizadas', [
            'project_id' => $project->id,
            'techs' => $techs,
        ]);
    }
}

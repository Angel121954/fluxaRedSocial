<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Technology;
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

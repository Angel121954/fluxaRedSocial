<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Badge;
use App\Models\Profile;
use App\Models\Technology;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ProfileService
{
    protected CloudinaryService $cloudinaryService;

    public function __construct(CloudinaryService $cloudinaryService)
    {
        $this->cloudinaryService = $cloudinaryService;
    }

    public function loadProfileData(User $user, ?User $viewer = null, bool $showFavorites = true): array
    {
        $viewerId = $viewer?->id;
        $isOwner = $viewerId === $user->id;

        $projects = $user->projects()
            ->with([
                'user',
                'media',
                'technologies',
                'likes' => fn ($q) => $q->where('user_id', $viewerId),
                'bookmarks' => fn ($q) => $q->where('user_id', $viewerId),
                'skillEndorsements',
            ])
            ->withCount(['media', 'likes', 'comments']);

        if (! $isOwner) {
            $projects->where('privacy', 'public');
        }

        $projects = $projects->latest()->get();

        $technologies = $user->technologies()->orderBy('category')->orderBy('name')->get();
        $allTechnologies = Technology::orderBy('category')->orderBy('name')->get();

        $workExperiences = $user->workExperiences()->orderBy('started_at', 'desc')->get();
        $educations = $user->educations()->orderBy('graduated_year', 'desc')->get();

        $favoriteProjects = collect();
        if ($showFavorites) {
            $favoriteProjects = $user->bookmarkedProjects()->latest()->get();

            $projectsById = $projects->keyBy('id');

            $favoriteProjects->each(function ($project) use ($projectsById) {
                if ($existing = $projectsById->get($project->id)) {
                    $project->setRelation('user', $existing->user);
                    $project->setRelation('media', $existing->media);
                    $project->setRelation('technologies', $existing->technologies);
                }
            });

            $needsLoad = $favoriteProjects->reject(fn ($p) => $projectsById->has($p->id));

            if ($needsLoad->isNotEmpty()) {
                $needsLoad->load(['user', 'media', 'technologies']);
            }
        }

        $badges = $user->badges()->get();
        $allBadges = Badge::orderBy('order')->get();

        $timeline = $this->getTimeline($projects, $workExperiences, $educations, $badges);

        return compact(
            'projects',
            'technologies',
            'allTechnologies',
            'workExperiences',
            'educations',
            'favoriteProjects',
            'badges',
            'allBadges',
            'timeline',
        ) + ['userTechnologies' => $technologies];
    }

    public function updateAvatar(int $userId, UploadedFile $file): string
    {
        $result = $this->cloudinaryService->uploadAvatar($file, (string) $userId);

        $profile = Profile::firstOrCreate(
            ['user_id' => $userId],
            ['avatar' => null, 'phone_code' => null, 'phone_number' => null, 'language' => 'es']
        );

        $profile->update(['avatar' => $result['secure_url']]);

        Log::info('Avatar actualizado', ['user_id' => $userId, 'avatar' => $result['secure_url']]);

        return $result['secure_url'];
    }

    public function deleteAvatar(int $userId): bool
    {
        $profile = Profile::where('user_id', $userId)->first();

        if (! $profile || ! $profile->avatar) {
            return false;
        }

        $publicId = $this->extractPublicId($profile->avatar);

        if ($publicId) {
            $this->cloudinaryService->delete($publicId);
        }

        $profile->update(['avatar' => null]);

        Log::info('Avatar eliminado', ['user_id' => $userId]);

        return true;
    }

    public function getTimeline(
        Collection $projects,
        Collection $workExperiences,
        Collection $educations,
        Collection $badges,
    ): Collection {
        $activities = collect();

        foreach ($projects as $project) {
            $activities->push([
                'type' => 'project',
                'date' => $project->created_at,
                'label' => 'Publicó un nuevo proyecto',
                'data' => $project,
            ]);
        }

        foreach ($workExperiences as $work) {
            $activities->push([
                'type' => 'work',
                'date' => $work->created_at,
                'label' => 'Agregó experiencia laboral',
                'data' => $work,
            ]);
        }

        foreach ($educations as $education) {
            $activities->push([
                'type' => 'education',
                'date' => $education->created_at,
                'label' => 'Agregó educación',
                'data' => $education,
            ]);
        }

        foreach ($badges as $badge) {
            $date = $badge->pivot?->earned_at ?? $badge->pivot?->created_at;
            if (! $date) {
                continue;
            }

            $activities->push([
                'type' => 'badge',
                'date' => Carbon::parse($date),
                'label' => 'Ganó una nueva insignia',
                'data' => $badge,
            ]);
        }

        return $activities->sortByDesc('date')->values();
    }

    protected function extractPublicId(string $url): ?string
    {
        if (! str_contains($url, 'cloudinary.com')) {
            return null;
        }

        $parts = explode('/upload/', $url);
        if (count($parts) !== 2) {
            return null;
        }

        $path = pathinfo($parts[1], PATHINFO_FILENAME);
        $publicId = 'fluxa/avatares/'.pathinfo($path, PATHINFO_BASENAME);

        return $publicId;
    }
}

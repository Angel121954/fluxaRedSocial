<?php

namespace App\Services;

use App\Models\Profile;
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

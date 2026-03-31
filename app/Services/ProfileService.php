<?php

namespace App\Services;

use App\Models\Profile;
use Illuminate\Http\UploadedFile;
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
        $publicId = 'avatares/'.pathinfo($path, PATHINFO_BASENAME);

        return $publicId;
    }
}

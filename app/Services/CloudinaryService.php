<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\ContentModerationException;
use Cloudinary\Cloudinary;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class CloudinaryService
{
    protected Cloudinary $cloudinary;

    public function __construct()
    {
        $this->cloudinary = new Cloudinary(config('cloudinary.cloud_url'));
    }

    public function upload(UploadedFile $file, string $folder, ?string $publicId = null, array $options = []): array
    {
        $uploadOptions = array_merge([
            'folder' => $folder,
            'resource_type' => 'auto',
        ], $options);

        if ($publicId) {
            $uploadOptions['public_id'] = $publicId;
            $uploadOptions['overwrite'] = true;
        }

        $result = $this->cloudinary->uploadApi()->upload($file->getRealPath(), $uploadOptions);
        $resultArray = $result->getArrayCopy();

        Log::info('Archivo subido a Cloudinary', [
            'public_id' => $resultArray['public_id'],
            'folder' => $folder,
            'format' => $resultArray['format'] ?? null,
            'bytes' => $resultArray['bytes'] ?? null,
        ]);

        $this->rejectIfModerated($resultArray);

        return $resultArray;
    }

    public function uploadAvatar(UploadedFile $file, string $userId): array
    {
        return $this->upload($file, 'fluxa/avatares', "user_{$userId}", array_merge([
            'transformation' => [[
                'width' => 400,
                'height' => 400,
                'crop' => 'fill',
                'gravity' => 'face',
                'quality' => 'auto',
                'fetch_format' => 'auto',
            ]],
        ], $this->moderationOptions()));
    }

    public function uploadProjectMedia(UploadedFile $file, int $position): array
    {
        $mime = $file->getMimeType();
        $resourceType = str_starts_with($mime, 'video/') ? 'video' : 'image';

        $options = ['resource_type' => $resourceType];

        if ($resourceType === 'image') {
            $options = array_merge($options, $this->moderationOptions());
        }

        return $this->upload($file, 'fluxa/projects', null, $options);
    }

    public function uploadSuggestionImage(UploadedFile $file, string $publicId): array
    {
        return $this->upload($file, 'fluxa/suggestions', $publicId, array_merge([
            'resource_type' => 'image',
        ], $this->moderationOptions()));
    }

    public function uploadFromUrl(string $url, string $folder, ?string $publicId = null, array $options = []): array
    {
        $uploadOptions = array_merge([
            'folder' => $folder,
            'resource_type' => 'image',
        ], $options);

        if ($publicId) {
            $uploadOptions['public_id'] = $publicId;
            $uploadOptions['overwrite'] = true;
        }

        $result = $this->cloudinary->uploadApi()->upload($url, $uploadOptions);
        $resultArray = $result->getArrayCopy();

        Log::info('Archivo subido a Cloudinary desde URL', [
            'public_id' => $resultArray['public_id'],
            'folder' => $folder,
            'format' => $resultArray['format'] ?? null,
            'bytes' => $resultArray['bytes'] ?? null,
        ]);

        return $resultArray;
    }

    public function uploadMessageMedia(UploadedFile $file, string $type): array
    {
        $mime = $file->getMimeType();
        $isImage = $type === 'image' || str_starts_with($mime, 'image/');

        $options = [
            'resource_type' => $isImage ? 'image' : 'auto',
            'use_filename' => true,
            'unique_filename' => true,
        ];

        if ($isImage) {
            $options['transformation'] = [
                'width' => 800,
                'crop' => 'limit',
                'quality' => 'auto:best',
                'fetch_format' => 'auto',
            ];
        }

        return $this->upload($file, 'fluxa/messages/images', null, $options);
    }

    private function moderationOptions(): array
    {
        $moderation = config('cloudinary.moderation');

        return $moderation ? ['moderation' => $moderation] : [];
    }

    private function rejectIfModerated(array $result): void
    {
        foreach ($result['moderation'] ?? [] as $moderation) {
            if (! in_array($moderation['status'] ?? null, ['rejected', 'aborted'], true)) {
                continue;
            }

            $publicId = $result['public_id'] ?? null;

            if ($publicId) {
                $this->delete($publicId, $result['resource_type'] ?? 'image');
            }

            Log::warning('Contenido rechazado por moderación', [
                'public_id' => $publicId,
                'kind' => $moderation['kind'] ?? null,
                'status' => $moderation['status'] ?? null,
            ]);

            throw new ContentModerationException;
        }
    }

    public function getImageUrl(string $publicId): string
    {
        return (string) $this->cloudinary->image($publicId)->toUrl();
    }

    public function delete(string $publicId, string $resourceType = 'image'): bool
    {
        try {
            $this->cloudinary->uploadApi()->destroy($publicId, [
                'resource_type' => $resourceType,
            ]);

            Log::info('Archivo eliminado de Cloudinary', [
                'public_id' => $publicId,
                'resource_type' => $resourceType,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error al eliminar archivo de Cloudinary', [
                'public_id' => $publicId,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}

<?php

namespace App\Services;

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

        return $resultArray;
    }

    public function uploadAvatar(UploadedFile $file, string $userId): array
    {
        return $this->upload($file, 'avatares', "user_{$userId}", [
            'transformation' => [[
                'width' => 400,
                'height' => 400,
                'crop' => 'fill',
                'gravity' => 'face',
                'quality' => 'auto',
                'fetch_format' => 'auto',
            ]],
        ]);
    }

    public function uploadProjectMedia(UploadedFile $file, int $position): array
    {
        $mime = $file->getMimeType();
        $resourceType = str_starts_with($mime, 'video/') ? 'video' : 'image';

        return $this->upload($file, 'projects', null, [
            'resource_type' => $resourceType,
        ]);
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

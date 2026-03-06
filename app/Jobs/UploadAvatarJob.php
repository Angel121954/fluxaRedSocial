<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class UploadAvatarJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected int $userId,
        protected string $tempPath
    ) {}

    public function handle(): void
    {
        $user = User::find($this->userId);

        $cloudinary = new \Cloudinary\Cloudinary(config('cloudinary.cloud_url'));
        $result = $cloudinary->uploadApi()->upload($this->tempPath, [
            'folder'         => 'avatares',
            'public_id'      => 'user_' . $this->userId,
            'overwrite'      => true,
            'transformation' => [
                [
                    'width'        => 400,
                    'height'       => 400,
                    'crop'         => 'fill',
                    'gravity'      => 'face',
                    'quality'      => 'auto',
                    'fetch_format' => 'auto',
                ],
            ],
        ]);

        $user->profile()->updateOrCreate(
            ['user_id' => $this->userId],
            ['avatar'  => $result['secure_url']]
        );
    }
}

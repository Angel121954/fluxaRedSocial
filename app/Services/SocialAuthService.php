<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\NotificationPreference;
use App\Models\Profile;
use App\Models\User;
use Cloudinary\Cloudinary;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\User as SocialiteUser;

class SocialAuthService
{
    public function findOrCreateUser(string $provider, SocialiteUser $socialUser): User
    {
        $avatarOriginal = $this->resolveAvatar($provider, $socialUser);

        $user = User::where('provider_id', $socialUser->getId())
            ->where('provider', $provider)
            ->first();

        if (! $user) {
            $user = User::where('email', $socialUser->getEmail())->first();
        }

        if (! $user) {
            $user = $this->createUser($provider, $socialUser, $avatarOriginal);
        } else {
            $user->update([
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
            ]);

            $this->updateAvatar($user, $avatarOriginal);
        }

        if ($provider === 'github') {
            $this->storeGithubToken($user, $socialUser);
            $this->syncGithubData($user);
        }

        NotificationPreference::firstOrCreate(
            ['user_id' => $user->id],
            [
                'email_enabled' => true,
                'push_enabled' => true,
                'notify_comments' => true,
                'notify_followers' => true,
                'notify_mentions' => true,
                'weekly_summary' => false,
            ]
        );

        $this->reactivateIfNeeded($user);

        return $user;
    }

    public function connectGithub(User $user, SocialiteUser $socialUser): void
    {
        $this->storeGithubToken($user, $socialUser);
        $this->syncGithubData($user);
    }

    private function createUser(string $provider, SocialiteUser $socialUser, string $avatarOriginal): User
    {
        return DB::transaction(function () use ($provider, $socialUser, $avatarOriginal): User {
            $baseUsername = Str::slug(
                $socialUser->getName() ?? $socialUser->getNickname() ?? 'user'
            );

            $baseUsername = $baseUsername ?: 'user';
            $username = $baseUsername;
            $count = 1;

            while (User::where('username', $username)->exists()) {
                $username = $baseUsername.$count++;
            }

            $user = User::create([
                'name' => $socialUser->getName() ?? $socialUser->getNickname(),
                'username' => $username,
                'email' => $socialUser->getEmail(),
                'password' => bcrypt(Str::random(16)),
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
                'status' => 'activo',
                'role' => 'user',
            ]);

            $user->forceFill(['email_verified_at' => now()])->save();

            Profile::create([
                'user_id' => $user->id,
                'avatar' => $this->uploadAvatarToCloudinary($avatarOriginal, $user->id),
            ]);

            return $user;
        });
    }

    private function updateAvatar(User $user, string $avatarOriginal): void
    {
        $currentAvatar = $user->profile?->avatar ?? '';
        $isAlreadyInCloudinary = str_contains($currentAvatar, 'cloudinary.com');

        $avatar = $isAlreadyInCloudinary
            ? $currentAvatar
            : $this->uploadAvatarToCloudinary($avatarOriginal, $user->id);

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            ['avatar' => $avatar]
        );
    }

    private function storeGithubToken(User $user, SocialiteUser $socialUser): void
    {
        $user->forceFill([
            'github_token' => $socialUser->token,
            'github_refresh_token' => $socialUser->refreshToken,
            'github_token_expires_at' => $socialUser->expiresIn
                ? now()->addSeconds($socialUser->expiresIn)
                : null,
        ])->save();
    }

    private function reactivateIfNeeded(User $user): void
    {
        if ($user->status === 'pending_deletion') {
            $user->forceFill([
                'status' => 'activo',
                'delete_at' => null,
            ])->save();

            return;
        }

        if ($user->status === 'inactivo') {
            $user->update(['status' => 'activo']);
        }
    }

    private function resolveAvatar(string $provider, SocialiteUser $socialUser): string
    {
        $avatar = $socialUser->getAvatar();

        return match ($provider) {
            'facebook' => preg_replace('/type=\w+/', 'type=large', $avatar),
            'google' => preg_replace('/=s\d+-c/', '=s400-c', $avatar),
            default => $avatar,
        };
    }

    private function uploadAvatarToCloudinary(string $avatarUrl, int $userId): string
    {
        try {
            Log::info('Intentando subir avatar a Cloudinary', [
                'user_id' => $userId,
                'avatar_url' => $avatarUrl,
            ]);

            $cloudinary = new Cloudinary(config('cloudinary.cloud_url'));
            $result = $cloudinary->uploadApi()->upload($avatarUrl, [
                'folder' => 'fluxa/avatares',
                'public_id' => 'user_'.$userId,
                'overwrite' => true,
                'transformation' => [
                    [
                        'width' => 400,
                        'height' => 400,
                        'crop' => 'fill',
                        'gravity' => 'face',
                        'quality' => 'auto',
                        'fetch_format' => 'auto',
                    ],
                ],
            ]);

            return $result['secure_url'];
        } catch (Exception $e) {
            Log::warning('Cloudinary upload failed, using original avatar.', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);

            return $avatarUrl;
        }
    }

    private function syncGithubData(User $user): void
    {
        if (! $user->github_token) {
            return;
        }

        try {
            $response = Http::withToken($user->github_token)
                ->accept('application/vnd.github.v3+json')
                ->get('https://api.github.com/user');

            if ($response->successful()) {
                $data = $response->json();

                $user->forceFill([
                    'github_username' => $data['login'] ?? $user->github_username,
                    'github_public_repos' => $data['public_repos'] ?? 0,
                    'github_synced_at' => now(),
                ])->save();
            }
        } catch (Exception $e) {
            Log::error('Error al sincronizar datos de GitHub', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}

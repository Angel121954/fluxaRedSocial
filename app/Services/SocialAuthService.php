<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\SocialAuthException;
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
        $email = $socialUser->getEmail();

        if (! $email) {
            throw new SocialAuthException('Tu cuenta no tiene un correo disponible.');
        }

        $avatarOriginal = $this->resolveAvatar($provider, $socialUser);
        $providerEmailVerified = $this->providerEmailIsVerified($provider, $socialUser);
        $isNewUser = false;

        $user = User::where('provider', $provider)
            ->where('provider_id', $socialUser->getId())
            ->first();

        if (! $user) {
            $user = User::where('email', $email)->first();

            if ($user) {
                $this->assertCanLinkAccount($user, $provider, $providerEmailVerified);
                $this->assertProviderNotLinkedToAnotherUser($provider, (string) $socialUser->getId(), $user->id);

                $user->update([
                    'provider' => $provider,
                    'provider_id' => $socialUser->getId(),
                ]);
            } else {
                $user = $this->createUser($provider, $socialUser, $avatarOriginal, $providerEmailVerified);
                $isNewUser = true;
            }
        }

        if ($user->status === 'banned') {
            throw new SocialAuthException('Tu cuenta ha sido suspendida.');
        }

        $this->updateAvatar($user, $avatarOriginal);

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

        if ($isNewUser && $user->email_verified_at === null) {
            $user->sendEmailVerificationNotification();
        }

        return $user;
    }

    public function connectGithub(User $user, SocialiteUser $socialUser): void
    {
        $this->storeGithubToken($user, $socialUser);
        $this->syncGithubData($user);
    }

    private function assertCanLinkAccount(User $user, string $provider, bool $providerEmailVerified): void
    {
        if ($user->status === 'banned') {
            throw new SocialAuthException('Tu cuenta ha sido suspendida.');
        }

        if ($user->email_verified_at === null) {
            throw new SocialAuthException(
                'Ya existe una cuenta con este correo sin verificar. Recupera el acceso con «Olvidé mi contraseña» o verifica tu correo antes de vincular el proveedor.'
            );
        }

        if (! $providerEmailVerified) {
            throw new SocialAuthException(
                'El proveedor no ha verificado tu correo. Verifícalo en el proveedor e intenta de nuevo.'
            );
        }

        if ($user->provider !== null && $user->provider !== $provider) {
            throw new SocialAuthException(
                'Ya existe una cuenta con este correo vinculada a otro proveedor. Inicia sesión con ese proveedor.'
            );
        }
    }

    private function assertProviderNotLinkedToAnotherUser(string $provider, string $providerId, int $userId): void
    {
        $linkedToAnotherUser = User::where('provider', $provider)
            ->where('provider_id', $providerId)
            ->where('id', '!=', $userId)
            ->exists();

        if ($linkedToAnotherUser) {
            throw new SocialAuthException('Esta cuenta social ya está vinculada a otro usuario de Fluxa.');
        }
    }

    private function createUser(
        string $provider,
        SocialiteUser $socialUser,
        ?string $avatarOriginal,
        bool $emailVerified,
    ): User {
        return DB::transaction(function () use ($provider, $socialUser, $avatarOriginal, $emailVerified): User {
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

            $user->forceFill([
                'email_verified_at' => $emailVerified ? now() : null,
            ])->save();

            Profile::create([
                'user_id' => $user->id,
                'avatar' => $avatarOriginal
                    ? $this->uploadAvatarToCloudinary($avatarOriginal, $user->id)
                    : null,
            ]);

            return $user;
        });
    }

    private function updateAvatar(User $user, ?string $avatarOriginal): void
    {
        if (empty($avatarOriginal)) {
            return;
        }

        $currentAvatar = $user->profile?->avatar ?? '';

        if (str_contains($currentAvatar, 'cloudinary.com')) {
            return;
        }

        $avatar = $this->uploadAvatarToCloudinary($avatarOriginal, $user->id);

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            ['avatar' => $avatar]
        );
    }

    private function storeGithubToken(User $user, SocialiteUser $socialUser): void
    {
        $expiresIn = $socialUser->expiresIn ?? null;

        $user->forceFill([
            'github_token' => $socialUser->token ?? null,
            'github_refresh_token' => $socialUser->refreshToken ?? null,
            'github_token_expires_at' => $expiresIn ? now()->addSeconds((int) $expiresIn) : null,
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

    private function providerEmailIsVerified(string $provider, SocialiteUser $socialUser): bool
    {
        $raw = method_exists($socialUser, 'getRaw') ? (array) $socialUser->getRaw() : [];

        return match ($provider) {
            'google' => filter_var($raw['email_verified'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'github' => $this->githubEmailIsVerified($socialUser),
            'facebook' => filter_var($raw['verified'] ?? false, FILTER_VALIDATE_BOOLEAN),
            default => false,
        };
    }

    private function githubEmailIsVerified(SocialiteUser $socialUser): bool
    {
        $email = strtolower((string) $socialUser->getEmail());

        if ($email === '') {
            return false;
        }

        try {
            $response = Http::withToken((string) ($socialUser->token ?? ''))
                ->accept('application/vnd.github.v3+json')
                ->get('https://api.github.com/user/emails');

            if (! $response->successful()) {
                return false;
            }

            foreach ((array) $response->json() as $item) {
                if (strtolower((string) ($item['email'] ?? '')) === $email) {
                    return (bool) ($item['verified'] ?? false);
                }
            }
        } catch (Exception $e) {
            Log::warning('No se pudo verificar el email de GitHub', [
                'error' => $e->getMessage(),
            ]);
        }

        return false;
    }

    private function resolveAvatar(string $provider, SocialiteUser $socialUser): ?string
    {
        $avatar = $socialUser->getAvatar();

        if (empty($avatar)) {
            return null;
        }

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

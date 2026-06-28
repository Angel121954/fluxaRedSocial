<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\NotificationPreference;
use App\Models\Profile;
use App\Models\User;
use Cloudinary\Cloudinary;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    protected array $allowedProviders = ['google', 'github', 'facebook'];

    public function redirect(string $provider)
    {
        if (! in_array($provider, $this->allowedProviders)) {
            abort(404);
        }

        return Socialite::driver($provider)->redirect();
    }

    protected function handleGithubImport(string $provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();

            $user = Auth::user();
            $user->forceFill([
                'github_token' => $socialUser->token,
                'github_refresh_token' => $socialUser->refreshToken,
                'github_token_expires_at' => $socialUser->expiresIn
                    ? now()->addSeconds($socialUser->expiresIn)
                    : null,
            ])->save();

            $this->syncGithubData($user);

            return redirect()->route('profile.index', ['github_import' => '1']);
        } catch (Exception $e) {
            Log::error('Error al conectar GitHub para importar', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('profile.index')
                ->with('error', 'No se pudo conectar con GitHub. Intenta de nuevo.');
        }
    }

    protected function resolveAvatar(string $provider, $socialUser): string
    {
        $avatar = $socialUser->getAvatar();

        return match ($provider) {
            'facebook' => preg_replace('/type=\w+/', 'type=large', $avatar),
            'google' => preg_replace('/=s\d+-c/', '=s400-c', $avatar),
            default => $avatar,
        };
    }

    protected function uploadAvatarToCloudinary(string $avatarUrl, string $userId): string
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

    public function callback(string $provider)
    {
        if (! in_array($provider, $this->allowedProviders)) {
            abort(404);
        }

        if (Auth::check() && $provider === 'github' && session('github_import_redirect')) {
            return $this->handleGithubImport($provider);
        }

        try {
            $driver = Socialite::driver($provider);

            $socialUser = $driver->user();

            if (! $socialUser->getEmail()) {
                return redirect()
                    ->route('login')
                    ->with('error', 'Tu cuenta no tiene email disponible.');
            }

            $avatarOriginal = $this->resolveAvatar($provider, $socialUser);

            $user = User::where('provider_id', $socialUser->getId())
                ->where('provider', $provider)
                ->first();

            if (! $user) {
                $user = User::where('email', $socialUser->getEmail())->first();
            }

            if (! $user) {
                $user = DB::transaction(function () use ($socialUser, $provider, $avatarOriginal) {

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

                    $avatar = $this->uploadAvatarToCloudinary($avatarOriginal, $user->id);

                    Profile::create([
                        'user_id' => $user->id,
                        'avatar' => $avatar,
                    ]);

                    return $user;
                });
            } else {
                $user->update([
                    'provider' => $provider,
                    'provider_id' => $socialUser->getId(),
                ]);

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

            if ($provider === 'github') {
                $user->forceFill([
                    'github_token' => $socialUser->token,
                    'github_refresh_token' => $socialUser->refreshToken,
                    'github_token_expires_at' => $socialUser->expiresIn
                        ? now()->addSeconds($socialUser->expiresIn)
                        : null,
                ])->save();

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

            if ($user->status === 'pending_deletion') {
                $user->forceFill([
                    'status' => 'activo',
                    'delete_at' => null,
                ])->save();
            }

            if ($user->status === 'inactivo') {
                $user->update(['status' => 'activo']);
            }

            if ($user->two_factor_secret &&
                in_array(TwoFactorAuthenticatable::class, class_uses_recursive($user))) {
                request()->session()->put([
                    'login.id' => $user->getKey(),
                    'login.remember' => true,
                ]);
                session()->put('url.intended', route('explore.index'));

                return redirect()->route('two-factor.login');
            }

            Auth::login($user, true);
            request()->session()->regenerate();

            return redirect()->route('explore.index');
        } catch (Exception $e) {
            report($e);

            return redirect()
                ->route('login')
                ->with('error', 'Error al autenticar con '.ucfirst($provider));
        }
    }

    protected function syncGithubData(User $user): void
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

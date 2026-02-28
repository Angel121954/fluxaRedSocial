<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class SocialAuthController extends Controller
{
    protected array $allowedProviders = ['google', 'github', 'facebook'];

    public function redirect(string $provider)
    {
        if (!in_array($provider, $this->allowedProviders)) {
            abort(404);
        }

        return Socialite::driver($provider)->redirect();
    }

    protected function resolveAvatar(string $provider, $socialUser): string
    {
        $avatar = $socialUser->getAvatar();

        return match ($provider) {
            'facebook' => preg_replace('/type=\w+/', 'type=large', $avatar),
            'google'   => preg_replace('/=s\d+-c/', '=s400-c', $avatar),
            default    => $avatar,
        };
    }

    /**
     * Sube el avatar a Cloudinary y retorna la URL permanente.
     * Si falla, retorna la URL original del proveedor como fallback.
     */
    protected function uploadAvatarToCloudinary(string $avatarUrl, string $userId): string
    {
        try {
            Log::info('Intentando subir avatar a Cloudinary', [
                'user_id'    => $userId,
                'avatar_url' => $avatarUrl,
            ]);

            $cloudinary = new \Cloudinary\Cloudinary(config('cloudinary.cloud_url'));
            $result     = $cloudinary->uploadApi()->upload($avatarUrl, [
                'folder'         => 'avatares',
                'public_id'      => 'user_' . $userId,
                'overwrite'      => true,
                'transformation' => [
                    [
                        'width'        => 400,
                        'height'       => 400,
                        'crop'         => 'fill',
                        'gravity'      => 'face',
                        'quality'      => 'auto',
                        'fetch_format' => 'auto',
                    ]
                ],
            ]);

            return $result['secure_url'];
        } catch (Exception $e) {
            Log::warning('Cloudinary upload failed, using original avatar.', [
                'user_id' => $userId,
                'error'   => $e->getMessage(),
            ]);

            return $avatarUrl;
        }
    }

    public function callback(string $provider)
    {
        if (!in_array($provider, $this->allowedProviders)) {
            abort(404);
        }

        try {
            $socialUser = Socialite::driver($provider)->user();

            if (!$socialUser->getEmail()) {
                return redirect()
                    ->route('login')
                    ->with('error', 'Tu cuenta no tiene email disponible.');
            }

            $avatarOriginal = $this->resolveAvatar($provider, $socialUser);

            $user = User::where('provider_id', $socialUser->getId())
                ->where('provider', $provider)
                ->first();

            if (!$user) {
                $user = User::where('email', $socialUser->getEmail())->first();
            }

            if (!$user) {
                $user = DB::transaction(function () use ($socialUser, $provider, $avatarOriginal) {

                    $baseUsername = Str::slug(
                        $socialUser->getName() ?? $socialUser->getNickname() ?? 'user'
                    );

                    $baseUsername = $baseUsername ?: 'user';
                    $username     = $baseUsername;
                    $count        = 1;

                    while (User::where('username', $username)->exists()) {
                        $username = $baseUsername . $count++;
                    }

                    $user = User::create([
                        'name'              => $socialUser->getName() ?? $socialUser->getNickname(),
                        'username'          => $username,
                        'email'             => $socialUser->getEmail(),
                        'email_verified_at' => now(),
                        'password'          => bcrypt(Str::random(16)),
                        'provider'          => $provider,
                        'provider_id'       => $socialUser->getId(),
                    ]);

                    // Subir a Cloudinary DESPUÉS de crear el user (necesitamos el ID)
                    $avatar = $this->uploadAvatarToCloudinary($avatarOriginal, $user->id);

                    Profile::create([
                        'user_id' => $user->id,
                        'avatar'  => $avatar,
                    ]);

                    return $user;
                });
            } else {
                $user->update([
                    'provider'    => $provider,
                    'provider_id' => $socialUser->getId(),
                ]);

                // Solo re-sube si el avatar del perfil NO es ya de Cloudinary
                $currentAvatar = $user->profile?->avatar ?? '';
                $isAlreadyInCloudinary = str_contains($currentAvatar, 'cloudinary.com');

                $avatar = $isAlreadyInCloudinary
                    ? $currentAvatar
                    : $this->uploadAvatarToCloudinary($avatarOriginal, $user->id);

                $user->profile()->updateOrCreate(
                    ['user_id' => $user->id],
                    ['avatar'  => $avatar]
                );
            }

            Auth::login($user, true);
            request()->session()->regenerate();

            return redirect()->route('explore.index');
        } catch (Exception $e) {
            report($e);

            return redirect()
                ->route('login')
                ->with('error', 'Error al autenticar con ' . ucfirst($provider));
        }
    }
}

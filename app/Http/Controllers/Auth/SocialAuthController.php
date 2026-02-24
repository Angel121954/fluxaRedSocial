<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Exception;

class SocialAuthController extends Controller
{
    protected array $allowedProviders = ['google', 'github'];

    public function redirect(string $provider)
    {
        if (!in_array($provider, $this->allowedProviders)) {
            abort(404);
        }

        if ($provider === 'github') {
            return Socialite::driver('github')
                ->redirect();
        }

        return Socialite::driver($provider)->redirect();
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

            $user = User::where('provider_id', $socialUser->getId())
                ->where('provider', $provider)
                ->first();

            if (!$user) {
                $user = User::where('email', $socialUser->getEmail())->first();
            }

            if (!$user) {
                $user = DB::transaction(function () use ($socialUser, $provider) {

                    $baseUsername = Str::slug(
                        $socialUser->getName() ?? $socialUser->getNickname()
                    );

                    if (!$baseUsername) {
                        $baseUsername = 'user';
                    }

                    $username = $baseUsername;
                    $count = 1;

                    while (User::where('username', $username)->exists()) {
                        $username = $baseUsername . $count;
                        $count++;
                    }

                    return User::create([
                        'name' => $socialUser->getName() ?? $socialUser->getNickname(),
                        'username' => $username,
                        'email' => $socialUser->getEmail(),
                        'password' => bcrypt(Str::random(16)),
                        'provider' => $provider,
                        'provider_id' => $socialUser->getId(),
                        'avatar' => $socialUser->getAvatar(),
                    ]);
                });
            } else {
                $user->update([
                    'provider' => $provider,
                    'provider_id' => $socialUser->getId(),
                    'avatar' => $socialUser->getAvatar(),
                ]);
            }

            Auth::login($user, true);
            request()->session()->regenerate();

            return redirect()->route('explore');
        } catch (Exception $e) {
            report($e);

            return redirect()
                ->route('login')
                ->with('error', 'Error al autenticar con ' . ucfirst($provider));
        }
    }
}

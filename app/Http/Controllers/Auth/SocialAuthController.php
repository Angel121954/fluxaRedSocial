<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SocialAuthService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;

class SocialAuthController extends Controller
{
    protected array $allowedProviders = ['google', 'github', 'facebook'];

    public function __construct(
        protected SocialAuthService $socialAuthService,
    ) {}

    public function redirect(string $provider): SymfonyRedirectResponse
    {
        if (! in_array($provider, $this->allowedProviders)) {
            abort(404);
        }

        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider): RedirectResponse
    {
        if (! in_array($provider, $this->allowedProviders)) {
            abort(404);
        }

        if (Auth::check() && $provider === 'github' && session('github_import_redirect')) {
            return $this->handleGithubImport($provider);
        }

        try {
            $socialUser = Socialite::driver($provider)->user();

            if (! $socialUser->getEmail()) {
                return redirect()
                    ->route('login')
                    ->with('error', 'Tu cuenta no tiene email disponible.');
            }

            $user = $this->socialAuthService->findOrCreateUser($provider, $socialUser);

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

    protected function handleGithubImport(string $provider): RedirectResponse
    {
        try {
            $socialUser = Socialite::driver($provider)->user();

            /** @var User $user */
            $user = Auth::user();

            $this->socialAuthService->connectGithub($user, $socialUser);

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
}

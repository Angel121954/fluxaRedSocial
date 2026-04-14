<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Support\Facades\Session;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        // 👇 chequea 2FA antes de regenerar sesión
        $user = Auth::user();
        /** @var \App\models\User $user */

        if ($user->hasEnabledTwoFactorAuthentication()) {
            // Guarda el ID en sesión para el challenge y cierra la sesión temporalmente
            Session::put('login.id', $user->getKey());
            Session::put('login.remember', $request->boolean('remember'));
            Auth::logout();

            return redirect()->route('two-factor.login');
        }

        $request->session()->regenerate();
        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}

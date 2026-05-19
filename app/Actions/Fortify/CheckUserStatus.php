<?php

declare(strict_types=1);

namespace App\Actions\Fortify;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CheckUserStatus
{
    public function handle(Request $request, Closure $next)
    {
        $credentials = $request->only('email', 'password');
        $user = Auth::getProvider()->retrieveByCredentials($credentials);

        if (! $user || ! Auth::getProvider()->validateCredentials($user, $credentials)) {
            RateLimiter::hit(Str::transliterate(Str::lower($request->input('email')).'|'.$request->ip()));

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        if ($user->status === 'banned') {
            throw ValidationException::withMessages([
                'email' => 'Tu cuenta ha sido suspendida'
                    .($user->ban_reason ? ": {$user->ban_reason}" : '.'),
            ]);
        }

        if ($user->status === 'pending_deletion') {
            throw ValidationException::withMessages([
                'email' => 'Tu cuenta está programada para eliminarse y no puede acceder.',
            ]);
        }

        if ($user->status === 'inactivo') {
            $user->update(['status' => 'activo']);
        }

        return $next($request);
    }
}

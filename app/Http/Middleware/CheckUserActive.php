<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserActive
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $status = Auth::user()->status;

            if (in_array($status, ['inactivo', 'banned', 'pending_deletion'], true)) {
                Auth::logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $message = match ($status) {
                    'banned' => 'Tu cuenta ha sido suspendida.',
                    'pending_deletion' => 'Tu cuenta está programada para eliminarse. Inicia sesión para cancelar la eliminación.',
                    default => 'Tu cuenta está desactivada.',
                };

                if ($request->expectsJson()) {
                    return response()->json(['message' => $message], 403);
                }

                return redirect()->route('login')
                    ->withErrors(['email' => $message]);
            }
        }

        return $next($request);
    }
}

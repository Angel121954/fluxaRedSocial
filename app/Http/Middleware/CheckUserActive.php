<?php

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

            if (in_array($status, ['inactivo', 'banned'], true)) {
                Auth::logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $message = $status === 'banned'
                    ? 'Tu cuenta ha sido suspendida.'
                    : 'Tu cuenta está desactivada.';

                return redirect()->route('login')
                    ->withErrors(['email' => $message]);
            }
        }

        return $next($request);
    }
} 

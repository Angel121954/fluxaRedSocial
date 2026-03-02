<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserActive
{
   public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->status === 'inactivo') {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['email' => 'Tu cuenta está desactivada.']);
        }

        return $next($request);
    }
} 

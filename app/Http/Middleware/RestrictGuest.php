<?php
// app/Http/Middleware/RestrictGuest.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RestrictGuest
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->role === 'guest') {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Crea una cuenta para usar esta función.'
                ], 403);
            }

            return redirect()->route('register')
                ->with('warning', 'Necesitas una cuenta para hacer esto.');
        }

        return $next($request);
    }
}
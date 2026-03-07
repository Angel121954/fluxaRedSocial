<?php
// app/Http/Middleware/CleanExpiredGuests.php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Carbon\Carbon;

class CleanExpiredGuests
{
    public function handle($request, Closure $next)
    {
        // Elimina visitantes creados hace más de 24 horas
        User::where('status', 'temporal')
            ->where('provider', 'guest')
            ->where('created_at', '<', Carbon::now()->subHours(24))
            ->delete();

        return $next($request);
    }
}
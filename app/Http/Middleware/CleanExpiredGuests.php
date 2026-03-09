<?php

namespace App\Http\Middleware;

use App\Models\User;
use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Log;

class CleanExpiredGuests
{
    public function handle($request, Closure $next)
    {
        $guests = User::where('status', 'temporal')
            ->where('provider', 'guest')
            ->where('created_at', '<', Carbon::now()->subHours(24))
            ->get();

        if ($guests->isNotEmpty()) {
            foreach ($guests as $guest) {
                $guest->delete();
            }
        }

        return $next($request);
    }
}

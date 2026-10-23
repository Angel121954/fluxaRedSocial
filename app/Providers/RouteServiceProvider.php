<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/explore';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($this->throttleKey($request));
        });

        RateLimiter::for('interactions', function (Request $request) {
            return Limit::perMinute(120)->by($this->throttleKey($request));
        });

        RateLimiter::for('comments', function (Request $request) {
            return Limit::perMinute(20)->by($this->throttleKey($request));
        });

        RateLimiter::for('follows', function (Request $request) {
            return Limit::perMinute(30)->by($this->throttleKey($request));
        });

        RateLimiter::for('reports', function (Request $request) {
            return Limit::perMinute(5)->by($this->throttleKey($request));
        });

        RateLimiter::for('uploads', function (Request $request) {
            return Limit::perMinute(10)->by($this->throttleKey($request));
        });

        RateLimiter::for('messages', function (Request $request) {
            return Limit::perMinute(60)->by($this->throttleKey($request));
        });

        RateLimiter::for('push', function (Request $request) {
            return Limit::perMinute(10)->by($this->throttleKey($request));
        });
    }

    private function throttleKey(Request $request): string
    {
        return (string) ($request->user()?->getAuthIdentifier() ?? $request->ip());
    }
}

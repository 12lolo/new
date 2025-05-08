<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        // Define rate limiting for API routes
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Register the routes
        $this->routes(function () {
            // In Laravel 12, there may be changes to how API middleware is configured
            // Let's try different approaches

            // Load API routes with minimal middleware to troubleshoot
            Route::prefix('api')
                ->group(function () {
                    // Add the api middleware here instead of in the group definition
                    Route::middleware('api')->group(base_path('routes/api.php'));
                });

            // Load web routes
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}

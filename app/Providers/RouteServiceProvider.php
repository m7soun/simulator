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
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $request = request();
        $version = strtolower($request->segment(2) ?: 'v1');

        $this->routes(function () use ($version, $request) {
            if (!file_exists(base_path('routes/api/' . $version . '/api.php'))) {
                abort(404, 'Not Found');
            }


            Route::prefix('api/' . $version)
                ->middleware('api')
                ->namespace('App\Http\Controllers\Api\\' . $version) // Set the default namespace here
                ->group(base_path('routes/api/' . $version . '/api.php'));
        });
    }
}

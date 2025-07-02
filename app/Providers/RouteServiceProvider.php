<?php

namespace App\Providers;

use App\Http\Middleware\ForceJsonResponse;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Route::prefix('api')
            ->middleware(['api', ForceJsonResponse::class])
            ->group(base_path('routes/api.php'));

        Route::prefix('api/v1')
            ->middleware(['auth:api', ForceJsonResponse::class])
            ->group(base_path('routes/api_v1.php'));
    }
}

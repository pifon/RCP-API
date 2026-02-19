<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::prefix('api')
            ->middleware([
                'api',
                \App\Http\Middleware\ForceJsonResponse::class,
                \App\Http\Middleware\ValidateJsonApi::class,
            ])
            ->group(base_path('routes/api.php'));

        Route::prefix('api/v1')
            ->middleware([
                'auth:api',
                \App\Http\Middleware\ForceJsonResponse::class,
                \App\Http\Middleware\ValidateJsonApi::class,
                \App\Http\Middleware\TieredRateLimit::class,
            ])
            ->group(base_path('routes/api_v1.php'));
    }
}

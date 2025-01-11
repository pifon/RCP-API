<?php

declare(strict_types=1);

namespace App\Providers;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class DoctrineServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(EntityManager::class, function (Application $app): EntityManager {
            $config = Setup::createAnnotationMetadataConfiguration(
                [base_path('app/Entities')], // Path to your entities
        true // Is dev mode?
            );

            /** @var array<string, mixed> $connection */
            $connection = config('doctrine.connection');

            return EntityManager::create($connection, $config);
        });
    }
}

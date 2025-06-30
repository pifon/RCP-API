<?php

declare(strict_types=1);

namespace App\Providers;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class DoctrineServiceProvider extends ServiceProvider
{
    public function register()
    {
        $config = ORMSetup::createAttributeMetadataConfiguration(
            $paths = [base_path('app/Entities')], // your entities folder
            $isDevMode = true //env('APP_DEBUG', false)
        );

        $connectionParams = config('doctrine.connection');

        $connection = DriverManager::getConnection($connectionParams, $config);

        new EntityManager($connection, $config);
    }
    /*

    public function register(): void
    {
        $this->app->singleton(EntityManager::class, function (Application $app): EntityManager {
            $config = Setup::createAnnotationMetadataConfiguration(
                [base_path('app/Entities')], // Path to your entities
        true // Is dev mode?
            );

            /** @var array<string, mixed> $connection *
            $connection = config('doctrine.connection');

            return EntityManager::create($connection, $config);
        });
    }
    */
}

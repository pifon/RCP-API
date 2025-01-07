<?php

namespace App\Providers;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Illuminate\Support\ServiceProvider;

class DoctrineServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(EntityManager::class, function ($app) {
            $config = Setup::createAnnotationMetadataConfiguration(
                [base_path('app/Entities')], // Path to your entities
        true // Is dev mode?
            );

            $connection = config('doctrine.connection');

            return EntityManager::create($connection, $config);
        });
    }
}

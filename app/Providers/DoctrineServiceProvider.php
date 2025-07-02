<?php

declare(strict_types=1);

namespace App\Providers;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Illuminate\Support\ServiceProvider;

class DoctrineServiceProvider extends ServiceProvider
{
    public function register()
    {
        $config = ORMSetup::createAttributeMetadataConfiguration(
            $paths = [base_path('app/Entities')],
            $isDevMode = true // env('APP_DEBUG', false)
        );

        $connectionParams = config('doctrine.connection');

        $connection = DriverManager::getConnection($connectionParams, $config);

        new EntityManager($connection, $config);
    }
}

<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\Auth\AppUserProvider;
// use App\Services\Auth\JWTGuard;
// use App\Services\Auth\JwtProvider;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Auth\AuthManager;
use Illuminate\Container\Container;
# use Illuminate\Contracts\Hashing\Hasher;
# use Illuminate\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;

class AuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->resolving('auth', callback: function (AuthManager $auth) {
            $auth->provider('app_user_provider', function (Container $app, array $config): AppUserProvider {
                $entity = $config['model'];
                $em = $app['registry']->getManagerForClass($entity);

                if (! $em) {
                    throw new InvalidArgumentException("No EntityManager is set-up for $entity");
                }

                return new AppUserProvider(
                    //$app->get(Hasher::class),
                    //$em,
                    //$entity,
                    $app['some_dependency'],
                    $app->make(EntityManagerInterface::class),
                    $entity
                );
            });
            /*
            $auth->extend('jwt', function ($app, $name, array $config) use ($auth) {
                $userProvider = $auth->createUserProvider($config['provider']);

                if ($userProvider === null) {
                    throw new InvalidArgumentException('Cannot create user provider: '.$config['provider']);
                }
                $jwtProvider = $app->get(JwtProvider::class);
                $dispatcher = $app->get(Dispatcher::class);

                return new JwtGuard($userProvider, $jwtProvider, $dispatcher);
            });
            */
        });
    }
}

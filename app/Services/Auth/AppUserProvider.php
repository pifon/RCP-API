<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Entities\User;
use App\Repositories\v1\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Hashing\Hasher;
use LaravelDoctrine\ORM\Auth\DoctrineUserProvider;

class AppUserProvider extends DoctrineUserProvider
{
    public function __construct(
        Hasher $hasher,
        EntityManagerInterface $em,
        string $entity
    ) {
        parent::__construct($hasher, $em, $entity);
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     *
     * @throws Exception
     */
    public function retrieveByToken(mixed $identifier, mixed $token): ?Authenticatable
    {
        /** @var UserRepository $repository */
        $repository = $this->getRepository();

        return $repository->getUserByToken(
            $identifier,
            $token
        );
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  string  $token
     *
     * @throws Exception
     */
    public function updateRememberToken(Authenticatable $user, mixed $token): void
    {
        throw new Exception('Not implemented.');
    }

    /**
     * Retrieve a user by the given credentials.
     */
    public function retrieveByCredentials(array $credentials): ?Authenticatable
    {
        if (empty($credentials['username']) || ! is_string($credentials['username'])) {
            return null;
        }

        /** @var UserRepository $repository */
        $repository = $this->getRepository();

        return $repository->getAuthedUser(
            $credentials['username'],
        );
    }

    /**
     * Validate a user against the given credentials.
     */
    public function validateCredentials(Authenticatable $user, array $credentials): bool
    {
        if (! $user instanceof User) {
            return false;
        }

        // do check password
        return $this->hasher->check(
            $credentials['password'],
            $user->getAuthPassword(),
        );
    }
}

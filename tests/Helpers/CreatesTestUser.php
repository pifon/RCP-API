<?php

declare(strict_types=1);

namespace Tests\Helpers;

use App\Entities\User;
use App\Exceptions\v1\ValidationErrorException;
use DateTime;

trait CreatesTestUser
{
    /**
     * @throws ValidationErrorException
     */
    protected function createOrGetTestUser(): User
    {
        $em = app('em');
        $userRepo = $em->getRepository(User::class);

        $user = $userRepo->findOneBy(['username' => 'test-user']);
        if (! $user) {
            $user = new User();
            $user->setUsername('test-user');
            $user->setEmail(fake()->unique()->safeEmail());
            $user->setName(fake()->name());
            $user->setCreatedAt();
            $user->setUpdatedAt();
            $user->setPasswordChangedAt(new DateTime());
        }

        $user->setPassword('Pa$swo[d_1234');
        $user->setUpdatedAt();
        $em->persist($user);
        $em->flush();

        return $user;
    }
}

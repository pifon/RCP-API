<?php

declare(strict_types=1);

namespace Tests\Helpers;

use App\Entities\Author;
use App\Entities\Plan;
use App\Entities\User;
use App\Entities\UserSubscription;
use App\Exceptions\v1\ValidationErrorException;
use DateTime;
use Doctrine\ORM\EntityManager;

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

        $this->ensureAuthor($em, $user);
        $this->ensurePremiumSubscription($em, $user);

        return $user;
    }

    private function ensureAuthor(EntityManager $em, User $user): void
    {
        $existing = $em->getRepository(Author::class)->findOneBy(['user' => $user]);
        if ($existing !== null) {
            return;
        }

        $author = new Author();
        $author->setUser($user);
        $author->setName($user->getUsername());
        $author->setEmail($user->getEmail());
        $em->persist($author);
        $em->flush();
    }

    private function ensurePremiumSubscription(EntityManager $em, User $user): void
    {
        $plan = $em->getRepository(Plan::class)->findOneBy(['slug' => 'premium']);
        if ($plan === null) {
            return;
        }

        $existing = $em->getRepository(UserSubscription::class)->findOneBy([
            'user' => $user,
            'status' => 'active',
        ]);
        if ($existing !== null) {
            return;
        }

        $sub = new UserSubscription();
        $sub->setUser($user);
        $sub->setPlan($plan);
        $sub->setBillingCycle('yearly');
        $sub->setCurrentPeriodStart(new DateTime('-1 day'));
        $sub->setCurrentPeriodEnd(new DateTime('+1 year'));
        $em->persist($sub);
        $em->flush();
    }
}

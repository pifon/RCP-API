<?php

declare(strict_types=1);

namespace App\Services;

use App\Entities\PlanFeature;
use App\Entities\User;
use App\Entities\UserSubscription;
use Doctrine\ORM\EntityManager;

class FeatureGate
{
    private const DEFAULT_PLAN_SLUG = 'free';

    public function __construct(
        private readonly EntityManager $em,
    ) {
    }

    public function activeSubscription(User $user): ?UserSubscription
    {
        return $this->em->getRepository(UserSubscription::class)
            ->createQueryBuilder('s')
            ->where('s.user = :user')
            ->andWhere('s.status IN (:statuses)')
            ->andWhere('s.currentPeriodEnd >= :now')
            ->setParameter('user', $user)
            ->setParameter('statuses', ['active', 'trialing'])
            ->setParameter('now', new \DateTime())
            ->orderBy('s.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function currentPlanSlug(User $user): string
    {
        $sub = $this->activeSubscription($user);

        return $sub !== null ? $sub->getPlan()->getSlug() : self::DEFAULT_PLAN_SLUG;
    }

    public function featureValue(User $user, string $feature): ?string
    {
        $planSlug = $this->currentPlanSlug($user);

        $result = $this->em->createQueryBuilder()
            ->select('pf.value')
            ->from(PlanFeature::class, 'pf')
            ->join('pf.plan', 'p')
            ->where('p.slug = :slug')
            ->andWhere('pf.feature = :feature')
            ->setParameter('slug', $planSlug)
            ->setParameter('feature', $feature)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $result['value'] ?? null;
    }

    /**
     * Returns the numeric limit for a feature, or null when unlimited / not configured.
     */
    public function featureLimit(User $user, string $feature): ?int
    {
        $value = $this->featureValue($user, $feature);

        if ($value === null || $value === 'unlimited') {
            return null;
        }

        return (int) $value;
    }

    public function canAccessPaidRecipes(User $user): bool
    {
        return $this->featureValue($user, 'paid_recipes') === 'true';
    }

    public function apiRateLimit(User $user): int
    {
        $value = $this->featureValue($user, 'api_rate_limit');

        if ($value === null || $value === 'unlimited') {
            return 10000;
        }

        return (int) $value;
    }
}

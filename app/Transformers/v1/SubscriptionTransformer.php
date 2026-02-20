<?php

declare(strict_types=1);

namespace App\Transformers\v1;

use App\Entities\UserSubscription;
use App\JsonApi\AbstractTransformer;

class SubscriptionTransformer extends AbstractTransformer
{
    public function getType(): string
    {
        return 'subscriptions';
    }

    public function getId(object $entity): string
    {
        /** @var UserSubscription $entity */
        return (string) $entity->getId();
    }

    public function selfLink(object $entity): string
    {
        /** @var UserSubscription $entity */
        return '/api/v1/me/subscription';
    }

    protected function attributes(object $entity): array
    {
        /** @var UserSubscription $entity */
        return [
            'billing-cycle' => $entity->getBillingCycle(),
            'status' => $entity->getStatus(),
            'is-active' => $entity->isActive(),
            'trial-ends-at' => $entity->getTrialEndsAt()?->format('c'),
            'current-period-start' => $entity->getCurrentPeriodStart()->format('c'),
            'current-period-end' => $entity->getCurrentPeriodEnd()->format('c'),
            'cancelled-at' => $entity->getCancelledAt()?->format('c'),
        ];
    }

    protected function relationships(object $entity): array
    {
        /** @var UserSubscription $entity */
        $plan = $entity->getPlan();

        return [
            'plan' => [
                'data' => [
                    'type' => 'plans',
                    'id' => $plan->getSlug(),
                ],
                'links' => [
                    'related' => '/api/v1/plans/'.$plan->getSlug(),
                ],
            ],
        ];
    }
}

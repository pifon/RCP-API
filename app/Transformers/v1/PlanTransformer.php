<?php

declare(strict_types=1);

namespace App\Transformers\v1;

use App\Entities\Plan;
use App\JsonApi\AbstractTransformer;

class PlanTransformer extends AbstractTransformer
{
    public function getType(): string
    {
        return 'plans';
    }

    public function getId(object $entity): string
    {
        /** @var Plan $entity */
        return $entity->getSlug();
    }

    public function selfLink(object $entity): string
    {
        /** @var Plan $entity */
        return '/api/v1/plans/' . $entity->getSlug();
    }

    protected function attributes(object $entity): array
    {
        /** @var Plan $entity */
        return [
            'name' => $entity->getName(),
            'slug' => $entity->getSlug(),
            'description' => $entity->getDescription(),
            'price-monthly' => (float) $entity->getPriceMonthly(),
            'price-yearly' => (float) $entity->getPriceYearly(),
            'currency' => $entity->getCurrency(),
            'is-free' => $entity->isFree(),
            'is-active' => $entity->isActive(),
        ];
    }
}

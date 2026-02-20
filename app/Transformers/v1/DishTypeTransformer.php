<?php

declare(strict_types=1);

namespace App\Transformers\v1;

use App\Entities\DishType;
use App\JsonApi\AbstractTransformer;

class DishTypeTransformer extends AbstractTransformer
{
    public function getType(): string
    {
        return 'dish-types';
    }

    public function getId(object $entity): string
    {
        /** @var DishType $entity */
        return $entity->getIdentifier();
    }

    public function selfLink(object $entity): string
    {
        /** @var DishType $entity */
        return '/api/v1/dish-types/'.$entity->getIdentifier();
    }

    protected function attributes(object $entity): array
    {
        /** @var DishType $entity */
        return [
            'name' => $entity->getName(),
            'created-at' => $entity->getCreatedAt()->format('c'),
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Transformers\v1;

use App\Entities\Allergen;
use App\JsonApi\AbstractTransformer;

class AllergenTransformer extends AbstractTransformer
{
    public function getType(): string
    {
        return 'allergens';
    }

    public function getId(object $entity): string
    {
        /** @var Allergen $entity */
        return $entity->getSlug();
    }

    public function selfLink(object $entity): string
    {
        /** @var Allergen $entity */
        return '/api/v1/allergens/' . $entity->getSlug();
    }

    protected function attributes(object $entity): array
    {
        /** @var Allergen $entity */
        return [
            'name' => $entity->getName(),
            'description' => $entity->getDescription(),
        ];
    }
}

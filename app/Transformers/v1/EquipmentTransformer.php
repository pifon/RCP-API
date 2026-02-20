<?php

declare(strict_types=1);

namespace App\Transformers\v1;

use App\Entities\Equipment;
use App\JsonApi\AbstractTransformer;

class EquipmentTransformer extends AbstractTransformer
{
    public function getType(): string
    {
        return 'equipment';
    }

    public function getId(object $entity): string
    {
        /** @var Equipment $entity */
        return $entity->getSlug();
    }

    public function selfLink(object $entity): string
    {
        /** @var Equipment $entity */
        return '/api/v1/equipment/'.$entity->getSlug();
    }

    protected function attributes(object $entity): array
    {
        /** @var Equipment $entity */
        return [
            'name' => $entity->getName(),
            'description' => $entity->getDescription(),
        ];
    }
}

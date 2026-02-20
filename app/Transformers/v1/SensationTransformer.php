<?php

declare(strict_types=1);

namespace App\Transformers\v1;

use App\Entities\Sensation;
use App\JsonApi\AbstractTransformer;

class SensationTransformer extends AbstractTransformer
{
    public function getType(): string
    {
        return 'sensations';
    }

    public function getId(object $entity): string
    {
        /** @var Sensation $entity */
        return $entity->getSlug();
    }

    public function selfLink(object $entity): string
    {
        /** @var Sensation $entity */
        return '/api/v1/sensations/' . $entity->getSlug();
    }

    protected function attributes(object $entity): array
    {
        /** @var Sensation $entity */
        return [
            'name' => $entity->getName(),
            'description' => $entity->getDescription(),
        ];
    }
}

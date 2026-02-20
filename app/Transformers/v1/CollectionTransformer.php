<?php

declare(strict_types=1);

namespace App\Transformers\v1;

use App\Entities\Collection;
use App\JsonApi\AbstractTransformer;

class CollectionTransformer extends AbstractTransformer
{
    public function getType(): string
    {
        return 'collections';
    }

    public function getId(object $entity): string
    {
        /** @var Collection $entity */
        return (string) $entity->getId();
    }

    public function selfLink(object $entity): string
    {
        /** @var Collection $entity */
        return '/api/v1/collections/'.$entity->getId();
    }

    protected function attributes(object $entity): array
    {
        /** @var Collection $entity */
        return [
            'name' => $entity->getName(),
            'slug' => $entity->getSlug(),
            'description' => $entity->getDescription(),
            'type' => $entity->getType(),
            'is-public' => $entity->isPublic(),
            'item-count' => $entity->getItems()->count(),
            'created-at' => $entity->getCreatedAt()->format('c'),
            'updated-at' => $entity->getUpdatedAt()->format('c'),
        ];
    }
}

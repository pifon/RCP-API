<?php

declare(strict_types=1);

namespace App\Transformers\v1;

use App\Entities\Cuisine;
use App\JsonApi\AbstractTransformer;

class CuisineTransformer extends AbstractTransformer
{
    public function getType(): string
    {
        return 'cuisines';
    }

    public function getId(object $entity): string
    {
        /** @var Cuisine $entity */
        return $entity->getSlug();
    }

    public function selfLink(object $entity): string
    {
        /** @var Cuisine $entity */
        return '/api/v1/cuisines/' . $entity->getSlug();
    }

    protected function attributes(object $entity): array
    {
        /** @var Cuisine $entity */
        return [
            'name' => $entity->getName(),
            'full-name' => $entity->getFullName(),
            'variant' => $entity->getVariant(),
            'description' => $entity->getDescription(),
            'created-at' => $entity->getCreatedAt()->format('c'),
        ];
    }

    protected function relationships(object $entity): array
    {
        /** @var Cuisine $entity */
        $rels = [];

        $parent = $entity->getParent();
        if ($parent !== null) {
            $rels['parent'] = [
                'data' => ['type' => 'cuisines', 'id' => $parent->getSlug()],
                'links' => [
                    'related' => '/api/v1/cuisines/' . $parent->getSlug(),
                ],
                'entity' => $parent,
                'transformer' => self::class,
            ];
        }

        return $rels;
    }
}

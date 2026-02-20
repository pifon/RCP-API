<?php

declare(strict_types=1);

namespace App\Transformers\v1;

use App\Entities\CuisineRequest;
use App\JsonApi\AbstractTransformer;

class CuisineRequestTransformer extends AbstractTransformer
{
    public function getType(): string
    {
        return 'cuisine-requests';
    }

    public function getId(object $entity): string
    {
        /** @var CuisineRequest $entity */
        return (string) $entity->getId();
    }

    public function selfLink(object $entity): string
    {
        /** @var CuisineRequest $entity */
        return '/api/v1/cuisine-requests/'.$entity->getId();
    }

    protected function attributes(object $entity): array
    {
        /** @var CuisineRequest $entity */
        return [
            'name' => $entity->getName(),
            'variant' => $entity->getVariant(),
            'full-name' => $entity->getFullName(),
            'description' => $entity->getDescription(),
            'status' => $entity->getStatus(),
            'admin-notes' => $entity->getAdminNotes(),
            'created-at' => $entity->getCreatedAt()->format('c'),
            'updated-at' => $entity->getUpdatedAt()->format('c'),
        ];
    }

    protected function relationships(object $entity): array
    {
        /** @var CuisineRequest $entity */
        $rels = [];

        $author = $entity->getRequestedBy();
        $rels['requested-by'] = [
            'data' => ['type' => 'authors', 'id' => $author->getIdentifier()],
        ];

        $cuisine = $entity->getCuisine();
        if ($cuisine !== null) {
            $rels['cuisine'] = [
                'data' => ['type' => 'cuisines', 'id' => $cuisine->getSlug()],
                'links' => [
                    'related' => '/api/v1/cuisines/'.$cuisine->getSlug(),
                ],
                'entity' => $cuisine,
                'transformer' => CuisineTransformer::class,
            ];
        }

        return $rels;
    }
}

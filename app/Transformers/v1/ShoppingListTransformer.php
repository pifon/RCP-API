<?php

declare(strict_types=1);

namespace App\Transformers\v1;

use App\Entities\ShoppingList;
use App\JsonApi\AbstractTransformer;

class ShoppingListTransformer extends AbstractTransformer
{
    public function getType(): string
    {
        return 'shopping-lists';
    }

    public function getId(object $entity): string
    {
        /** @var ShoppingList $entity */
        return (string) $entity->getId();
    }

    public function selfLink(object $entity): string
    {
        /** @var ShoppingList $entity */
        return '/api/v1/shopping-lists/'.$entity->getId();
    }

    protected function attributes(object $entity): array
    {
        /** @var ShoppingList $entity */
        return [
            'name' => $entity->getName(),
            'status' => $entity->getStatus(),
            'item-count' => $entity->getItems()->count(),
            'checked-count' => $entity->getItems()->filter(
                fn ($i) => $i->isChecked()
            )->count(),
            'created-at' => $entity->getCreatedAt()->format('c'),
            'updated-at' => $entity->getUpdatedAt()->format('c'),
        ];
    }

    protected function relationships(object $entity): array
    {
        /** @var ShoppingList $entity */
        $rels = [];

        $collection = $entity->getCollection();
        if ($collection !== null) {
            $rels['collection'] = [
                'data' => [
                    'type' => 'collections',
                    'id' => (string) $collection->getId(),
                ],
            ];
        }

        return $rels;
    }
}

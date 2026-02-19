<?php

declare(strict_types=1);

namespace App\Transformers\v1;

use App\Entities\ShoppingListItem;
use App\JsonApi\AbstractTransformer;

class ShoppingListItemTransformer extends AbstractTransformer
{
    public function getType(): string
    {
        return 'shopping-list-items';
    }

    public function getId(object $entity): string
    {
        /** @var ShoppingListItem $entity */
        return (string) $entity->getId();
    }

    public function selfLink(object $entity): string
    {
        /** @var ShoppingListItem $entity */
        $listId = $entity->getShoppingList()->getId();

        return "/api/v1/shopping-lists/{$listId}/items/{$entity->getId()}";
    }

    protected function attributes(object $entity): array
    {
        /** @var ShoppingListItem $entity */
        return [
            'quantity' => (float) $entity->getQuantity(),
            'checked' => $entity->isChecked(),
            'created-at' => $entity->getCreatedAt()->format('c'),
            'updated-at' => $entity->getUpdatedAt()->format('c'),
        ];
    }

    protected function relationships(object $entity): array
    {
        /** @var ShoppingListItem $entity */
        $product = $entity->getProduct();
        $rels = [
            'product' => [
                'data' => [
                    'type' => 'products',
                    'id' => (string) $product->getId(),
                ],
            ],
        ];

        $measure = $entity->getMeasure();
        if ($measure !== null) {
            $rels['measure'] = [
                'data' => [
                    'type' => 'measures',
                    'id' => (string) $measure->getId(),
                ],
            ];
        }

        $recipe = $entity->getRecipe();
        if ($recipe !== null) {
            $rels['recipe'] = [
                'data' => [
                    'type' => 'recipes',
                    'id' => $recipe->getSlug(),
                ],
            ];
        }

        return $rels;
    }
}

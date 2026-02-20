<?php

declare(strict_types=1);

namespace App\Transformers\v1;

use App\Entities\CollectionItem;
use App\JsonApi\AbstractTransformer;

class CollectionItemTransformer extends AbstractTransformer
{
    public function getType(): string
    {
        return 'collection-items';
    }

    public function getId(object $entity): string
    {
        /** @var CollectionItem $entity */
        return (string) $entity->getId();
    }

    public function selfLink(object $entity): string
    {
        /** @var CollectionItem $entity */
        $collectionId = $entity->getCollection()->getId();

        return "/api/v1/collections/{$collectionId}/items/{$entity->getId()}";
    }

    protected function attributes(object $entity): array
    {
        /** @var CollectionItem $entity */
        return [
            'position' => $entity->getPosition(),
            'scheduled-date' => $entity->getScheduledDate()?->format('Y-m-d'),
            'meal-slot' => $entity->getMealSlot(),
            'note' => $entity->getNote(),
        ];
    }

    protected function relationships(object $entity): array
    {
        /** @var CollectionItem $entity */
        $recipe = $entity->getRecipe();

        return [
            'recipe' => [
                'data' => ['type' => 'recipes', 'id' => $recipe->getSlug()],
                'links' => [
                    'related' => '/api/v1/recipes/'.$recipe->getSlug(),
                ],
                'entity' => $recipe,
                'transformer' => RecipeTransformer::class,
            ],
        ];
    }
}

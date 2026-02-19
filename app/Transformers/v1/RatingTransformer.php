<?php

declare(strict_types=1);

namespace App\Transformers\v1;

use App\Entities\Rating;
use App\JsonApi\AbstractTransformer;

class RatingTransformer extends AbstractTransformer
{
    public function getType(): string
    {
        return 'ratings';
    }

    public function getId(object $entity): string
    {
        /** @var Rating $entity */
        return (string) $entity->getId();
    }

    public function selfLink(object $entity): string
    {
        /** @var Rating $entity */
        $recipeSlug = $entity->getRecipe()->getSlug();

        return "/api/v1/recipes/{$recipeSlug}/ratings/{$entity->getId()}";
    }

    protected function attributes(object $entity): array
    {
        /** @var Rating $entity */
        return [
            'rate' => $entity->getRate(),
            'created-at' => $entity->getCreatedAt()->format('c'),
            'updated-at' => $entity->getUpdatedAt()->format('c'),
        ];
    }

    protected function relationships(object $entity): array
    {
        /** @var Rating $entity */
        return [
            'user' => [
                'data' => [
                    'type' => 'users',
                    'id' => $entity->getUser()->getUsername(),
                ],
            ],
            'recipe' => [
                'data' => [
                    'type' => 'recipes',
                    'id' => $entity->getRecipe()->getSlug(),
                ],
            ],
        ];
    }
}

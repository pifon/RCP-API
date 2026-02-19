<?php

declare(strict_types=1);

namespace App\Transformers\v1;

use App\Entities\Recipe;
use App\JsonApi\AbstractTransformer;

class RecipeTransformer extends AbstractTransformer
{
    public function getType(): string
    {
        return 'recipes';
    }

    public function getId(object $entity): string
    {
        /** @var Recipe $entity */
        return $entity->getSlug();
    }

    public function selfLink(object $entity): string
    {
        /** @var Recipe $entity */
        return '/api/v1/recipes/' . $entity->getSlug();
    }

    protected function attributes(object $entity): array
    {
        /** @var Recipe $entity */
        return [
            'title' => $entity->getTitle(),
            'description' => $entity->getDescription(),
            'status' => $entity->getStatus(),
            'prep-time-minutes' => $entity->getPrepTimeMinutes(),
            'cook-time-minutes' => $entity->getCookTimeMinutes(),
            'total-time-minutes' => $entity->getTotalTimeMinutes(),
            'difficulty' => $entity->getDifficulty(),
            'serves' => $entity->getServes(),
            'source-url' => $entity->getSourceUrl(),
            'source-description' => $entity->getSourceDescription(),
            'price' => $entity->getPrice(),
            'currency' => $entity->getCurrency(),
            'is-free' => $entity->isFree(),
            'published-at' => $entity->getPublishedAt()?->format('c'),
            'created-at' => $entity->getCreatedAt()->format('c'),
            'updated-at' => $entity->getUpdatedAt()->format('c'),
        ];
    }

    protected function relationships(object $entity): array
    {
        /** @var Recipe $entity */
        $rels = [];
        $slug = $entity->getSlug();

        $author = $entity->getAuthor();
        $rels['author'] = [
            'data' => ['type' => 'authors', 'id' => $author->getIdentifier()],
            'links' => [
                'self' => "/api/v1/recipes/{$slug}/relationships/author",
                'related' => "/api/v1/recipes/{$slug}/author",
            ],
            'entity' => $author,
            'transformer' => AuthorTransformer::class,
        ];

        $cuisine = $entity->getCuisine();
        if ($cuisine !== null) {
            $rels['cuisine'] = [
                'data' => ['type' => 'cuisines', 'id' => $cuisine->getIdentifier()],
                'links' => [
                    'self' => "/api/v1/recipes/{$slug}/relationships/cuisine",
                    'related' => "/api/v1/recipes/{$slug}/cuisine",
                ],
                'entity' => $cuisine,
                'transformer' => CuisineTransformer::class,
            ];
        }

        $dishType = $entity->getDishType();
        if ($dishType !== null) {
            $rels['dish-type'] = [
                'data' => ['type' => 'dish-types', 'id' => $dishType->getIdentifier()],
                'links' => [
                    'self' => "/api/v1/recipes/{$slug}/relationships/dish-type",
                    'related' => "/api/v1/recipes/{$slug}/dish-type",
                ],
                'entity' => $dishType,
                'transformer' => DishTypeTransformer::class,
            ];
        }

        $cuisineRequest = $entity->getCuisineRequest();
        if ($cuisineRequest !== null) {
            $rels['cuisine-request'] = [
                'data' => ['type' => 'cuisine-requests', 'id' => (string) $cuisineRequest->getId()],
                'links' => [
                    'related' => '/api/v1/cuisine-requests/' . $cuisineRequest->getId(),
                ],
                'entity' => $cuisineRequest,
                'transformer' => CuisineRequestTransformer::class,
            ];
        }

        $forkedFrom = $entity->getForkedFrom();
        if ($forkedFrom !== null) {
            $rels['forked-from'] = [
                'data' => ['type' => 'recipes', 'id' => $forkedFrom->getIdentifier()],
                'links' => [
                    'related' => "/api/v1/recipes/{$forkedFrom->getSlug()}",
                ],
            ];
        }

        return $rels;
    }
}

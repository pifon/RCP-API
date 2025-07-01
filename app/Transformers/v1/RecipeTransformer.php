<?php

namespace App\Transformers\v1;

use DateTimeInterface;

class RecipeTransformer extends TransformerAbstract
{
    /**
     * {@inheritDoc}
     */
    public function transform(mixed $item): array
    {
        return [
            'title' => $item->getTitle(),
            // 'dish-type' => $item->getDishType(),
            '_links' => [
                'self' => route('recipes.show', ['slug' => $item->getSlug()]),
                'details' => route('recipes.details', ['slug' => $item->getSlug()]),
            ],
        ];
    }

    public function transformDetailed(mixed $item): array
    {
        return [
            'title' => $item->getTitle(),
            'details' => $item->getDescription(),
            'author' => $item->getAuthor(),
            // 'variant' => $item->getVariant(),
            'created_at' => $item->getCreatedAt()->format(DateTimeInterface::ATOM),
            'updated_at' => $item->getUpdatedAt()->format(DateTimeInterface::ATOM),
            '_links' => $this->getDetailedLinks($item),
        ];
    }

    private function getDetailedLinks(mixed $item): array
    {
        return [
            'self' => route('recipes.details', ['slug' => $item->getSlug()]),
            'handle' => route('recipes.show', ['slug' => $item->getSlug()]),
            // 'ingredients' => route('author.recipes', ['username' => $item->getUsername()]),
            // 'procedures' => route('author.cuisines', ['username' => $item->getUsername()]),
            // 'cuisine' => route('author.cuisines', ['username' => $item->getUsername()]),
            // 'related' => route('author.related', ['username' => $item->getUsername()]),
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Transformers;

use App\Entities\Cuisine;
use DateTimeInterface;

/**
 * @extends TransformerAbstract<Cuisine>
 */
class CuisineTransformer extends TransformerAbstract
{

    /**
     * @param Cuisine $item
     * @return array<string, mixed> Transferred cuisine item representation
     */
    public function transform(mixed $item): array
    {
        return [
            'name' => $item->getFullName(),
            '_links' => $this->getLinks($item),
        ];
    }

    private function getLinks(mixed $item): array
    {
        return [
            'self' => route('cuisines.show', ['slug' => $item->getSlug()]),
            'details' => route('cuisines.details', ['slug' => $item->getSlug()]),
        ];
    }

    public function transformDetailed(mixed $item): array
    {
        return [
            'name' => $item->getFullName(),
            'description' => $item->getDescription(),
            'created_at' => $item->getCreatedAt()->format(DateTimeInterface::ATOM),
            'updated_at' => $item->getUpdatedAt()->format(DateTimeInterface::ATOM),
            '_links' => $this->getDetailedLinks($item),
        ];
    }

    private function getDetailedLinks(mixed $item): array
    {
        return [
            'self' => route('cuisines.details', ['slug' => $item->getSlug()]),
            'handle' => route('cuisines.show', ['slug' => $item->getSlug()]),
            'recipes' => route('cuisines.show', ['slug' => $item->getSlug()]),
            'authors' => route('cuisines.authors', ['slug' => $item->getSlug()]),
            'ingredients' => route('cuisines.show', ['slug' => $item->getSlug()]),
            'related' => route('cuisines.show', ['slug' => $item->getSlug()]),
        ];
    }
}
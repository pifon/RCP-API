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
            'description' => $item->getDescription(),
            'createdAt' => $item->getCreatedAt()->format(DateTimeInterface::ATOM),
            'updatedAt' => $item->getUpdatedAt()->format(DateTimeInterface::ATOM),
            '_links' => $this->getLinks($item),
        ];
    }

    private function getLinks(mixed $item): array
    {
        return [
            'self' => route('cuisines.show', ['slug' => $item->getSlug()]),
        ];
    }
}
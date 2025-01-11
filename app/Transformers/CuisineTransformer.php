<?php

declare(strict_types=1);

namespace App\Transformers;

use App\Entities\Cuisine;

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
            'description' => $item->getDescription()
        ];
    }
}
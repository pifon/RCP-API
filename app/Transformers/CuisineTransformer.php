<?php

namespace App\Transformers;



use App\Entities\Cuisine;

/**
 * @extends TransformerAbstract<Cuisine>
 */
class CuisineTransformer extends TransformerAbstract
{

    public function transform(Cuisine $cuisine): array
    {
        return [
            'name' => $cuisine->getFullName(),
            'description' => $cuisine->getDescription()
        ];
    }
}
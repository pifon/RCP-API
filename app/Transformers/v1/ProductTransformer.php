<?php

declare(strict_types=1);

namespace App\Transformers\v1;

use App\Entities\Product;
use App\JsonApi\AbstractTransformer;

class ProductTransformer extends AbstractTransformer
{
    public function getType(): string
    {
        return 'products';
    }

    public function getId(object $entity): string
    {
        /** @var Product $entity */
        return $entity->getSlug();
    }

    public function selfLink(object $entity): string
    {
        /** @var Product $entity */
        return '/api/v1/products/' . $entity->getSlug();
    }

    protected function attributes(object $entity): array
    {
        /** @var Product $entity */
        return [
            'name' => $entity->getName(),
            'description' => $entity->getDescription(),
            'vegan' => $entity->isVegan(),
            'vegetarian' => $entity->isVegetarian(),
            'halal' => $entity->isHalal(),
            'kosher' => $entity->isKosher(),
            'taste' => [
                'sweet' => $entity->getTasteSweet(),
                'sour' => $entity->getTasteSour(),
                'salty' => $entity->getTasteSalty(),
                'bitter' => $entity->getTasteBitter(),
                'umami' => $entity->getTasteUmami(),
            ],
            'nutrition-per-100g' => [
                'calories' => $entity->getCaloriesPer100g(),
                'protein' => $entity->getProteinPer100g(),
                'carbs' => $entity->getCarbsPer100g(),
                'fat' => $entity->getFatPer100g(),
                'fiber' => $entity->getFiberPer100g(),
            ],
            'shelf-life-days' => $entity->getShelfLifeDays(),
            'shelf-life-opened-days' => $entity->getShelfLifeOpenedDays(),
            'created-at' => $entity->getCreatedAt()->format('c'),
        ];
    }

    protected function relationships(object $entity): array
    {
        /** @var Product $entity */
        $rels = [];

        $measure = $entity->getMeasure();
        if ($measure !== null) {
            $rels['default-measure'] = [
                'data' => ['type' => 'measures', 'id' => $measure->getSlug()],
            ];
        }

        return $rels;
    }
}

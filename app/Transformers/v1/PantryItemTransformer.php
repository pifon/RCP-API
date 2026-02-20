<?php

declare(strict_types=1);

namespace App\Transformers\v1;

use App\Entities\PantryItem;
use App\JsonApi\AbstractTransformer;

class PantryItemTransformer extends AbstractTransformer
{
    public function getType(): string
    {
        return 'pantry-items';
    }

    public function getId(object $entity): string
    {
        /** @var PantryItem $entity */
        return (string) $entity->getId();
    }

    public function selfLink(object $entity): string
    {
        /** @var PantryItem $entity */
        return '/api/v1/pantry/'.$entity->getId();
    }

    protected function attributes(object $entity): array
    {
        /** @var PantryItem $entity */
        return [
            'quantity' => (float) $entity->getQuantity(),
            'expires-at' => $entity->getExpiresAt()?->format('Y-m-d'),
            'best-before' => $entity->getBestBefore()?->format('Y-m-d'),
            'opened-at' => $entity->getOpenedAt()?->format('Y-m-d'),
            'is-expired' => $entity->isExpired(),
            'is-past-best-before' => $entity->isPastBestBefore(),
            'created-at' => $entity->getCreatedAt()->format('c'),
            'updated-at' => $entity->getUpdatedAt()->format('c'),
        ];
    }

    protected function relationships(object $entity): array
    {
        /** @var PantryItem $entity */
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

        return $rels;
    }
}

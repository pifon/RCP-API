<?php

namespace App\Transformers\v1;

use DateTimeInterface;

class DishTypeTransformer extends TransformerAbstract
{
    /**
     * {@inheritDoc}
     */
    #[\Override]
    public function transform(mixed $item): array
    {
        return [
            'name' => $item->getName(),
            '_links' => [
                'self' => route('dishtypes.show', ['slug' => $item->getName()]),
            ],
        ];
    }

    public function transformDetailed(mixed $item): array
    {
        return [
            'name' => $item->getName(),
            'created_at' => $item->getCreatedAt()->format(DateTimeInterface::ATOM),
            'updated_at' => $item->getUpdatedAt()->format(DateTimeInterface::ATOM),
            '_links' => $this->getDetailedLinks($item),
        ];
    }

    private function getDetailedLinks(mixed $item): array
    {
        return [
            'self' => route('dishtypes.show', ['slug' => $item->getName()]),
        ];
    }
}

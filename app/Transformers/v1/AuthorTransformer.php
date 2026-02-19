<?php

declare(strict_types=1);

namespace App\Transformers\v1;

use App\Entities\Author;
use App\JsonApi\AbstractTransformer;

class AuthorTransformer extends AbstractTransformer
{
    public function getType(): string
    {
        return 'authors';
    }

    public function getId(object $entity): string
    {
        /** @var Author $entity */
        return $entity->getIdentifier();
    }

    public function selfLink(object $entity): string
    {
        /** @var Author $entity */
        return '/api/v1/authors/' . $entity->getIdentifier();
    }

    protected function attributes(object $entity): array
    {
        /** @var Author $entity */
        return [
            'name' => $entity->getName(),
            'username' => $entity->getUsername(),
            'description' => $entity->getDescription(),
            'tier' => $entity->getTier(),
            'created-at' => $entity->getCreatedAt()->format('c'),
        ];
    }
}

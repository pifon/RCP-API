<?php

declare(strict_types=1);

namespace App\Transformers\v1;

use App\Entities\User;
use App\JsonApi\AbstractTransformer;

class UserTransformer extends AbstractTransformer
{
    public function getType(): string
    {
        return 'users';
    }

    public function getId(object $entity): string
    {
        /** @var User $entity */
        return $entity->getUsername();
    }

    public function selfLink(object $entity): string
    {
        return '/api/v1/me';
    }

    protected function attributes(object $entity): array
    {
        /** @var User $entity */
        return [
            'username' => $entity->getUsername(),
            'name' => $entity->getName(),
            'email' => $entity->getEmail(),
            'created-at' => $entity->getCreatedAt()?->format('c'),
        ];
    }
}

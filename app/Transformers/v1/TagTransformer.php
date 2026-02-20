<?php

declare(strict_types=1);

namespace App\Transformers\v1;

use App\Entities\Tag;
use App\JsonApi\AbstractTransformer;

class TagTransformer extends AbstractTransformer
{
    public function getType(): string
    {
        return 'tags';
    }

    public function getId(object $entity): string
    {
        /** @var Tag $entity */
        return $entity->getSlug();
    }

    public function selfLink(object $entity): string
    {
        /** @var Tag $entity */
        return '/api/v1/tags/'.$entity->getSlug();
    }

    protected function attributes(object $entity): array
    {
        /** @var Tag $entity */
        return [
            'name' => $entity->getName(),
            'group' => $entity->getGroup(),
        ];
    }
}

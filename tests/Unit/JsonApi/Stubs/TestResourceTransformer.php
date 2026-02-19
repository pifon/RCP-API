<?php

declare(strict_types=1);

namespace Tests\Unit\JsonApi\Stubs;

use App\JsonApi\AbstractTransformer;

class TestResourceTransformer extends AbstractTransformer
{
    public function getType(): string
    {
        return 'test-resources';
    }

    public function getId(object $entity): string
    {
        return (string) $entity->id;
    }

    public function selfLink(object $entity): string
    {
        return '/test/' . $entity->id;
    }

    protected function attributes(object $entity): array
    {
        return [
            'title' => $entity->title,
            'status' => $entity->status,
        ];
    }
}

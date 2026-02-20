<?php

declare(strict_types=1);

namespace Tests\Unit\JsonApi\Stubs;

use App\JsonApi\AbstractTransformer;

class StubTransformer extends AbstractTransformer
{
    public function getType(): string
    {
        return 'stubs';
    }

    public function getId(object $entity): string
    {
        return (string) $entity->id;
    }

    public function selfLink(object $entity): string
    {
        return '/stubs/'.$entity->id;
    }

    protected function attributes(object $entity): array
    {
        return [
            'name' => $entity->name ?? 'unknown',
            'extra' => 'value',
        ];
    }
}

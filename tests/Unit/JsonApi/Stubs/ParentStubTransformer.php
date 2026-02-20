<?php

declare(strict_types=1);

namespace Tests\Unit\JsonApi\Stubs;

use App\JsonApi\AbstractTransformer;

class ParentStubTransformer extends AbstractTransformer
{
    public function getType(): string
    {
        return 'parents';
    }

    public function getId(object $entity): string
    {
        return (string) $entity->id;
    }

    public function selfLink(object $entity): string
    {
        return '/parents/'.$entity->id;
    }

    protected function attributes(object $entity): array
    {
        return ['name' => $entity->name ?? ''];
    }
}

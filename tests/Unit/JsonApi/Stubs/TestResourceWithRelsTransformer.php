<?php

declare(strict_types=1);

namespace Tests\Unit\JsonApi\Stubs;

class TestResourceWithRelsTransformer extends TestResourceTransformer
{
    protected function relationships(object $entity): array
    {
        return [
            'parent' => [
                'data' => ['type' => 'parents', 'id' => '99'],
                'links' => ['related' => '/parents/99'],
                'entity' => (object) ['id' => '99', 'name' => 'Parent'],
                'transformer' => ParentStubTransformer::class,
            ],
        ];
    }
}

<?php

declare(strict_types=1);

namespace Tests\Unit\JsonApi;

use App\JsonApi\QueryParameters;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Unit\JsonApi\Stubs\TestResourceTransformer;
use Tests\Unit\JsonApi\Stubs\TestResourceWithRelsTransformer;

class AbstractTransformerTest extends TestCase
{
    #[Test]
    public function to_resource_basic_structure(): void
    {
        $entity = (object) ['id' => '5', 'title' => 'Test', 'status' => 'draft'];
        $transformer = new TestResourceTransformer;

        $resource = $transformer->toResource($entity);

        $this->assertSame('test-resources', $resource['type']);
        $this->assertSame('5', $resource['id']);
        $this->assertSame('Test', $resource['attributes']['title']);
        $this->assertSame('draft', $resource['attributes']['status']);
        $this->assertSame('/test/5', $resource['links']['self']);
    }

    #[Test]
    public function sparse_fieldsets_filters_attributes(): void
    {
        $entity = (object) ['id' => '1', 'title' => 'Hello', 'status' => 'published'];
        $params = new QueryParameters(fields: ['test-resources' => ['title']]);

        $resource = (new TestResourceTransformer)->toResource($entity, $params);

        $this->assertArrayHasKey('title', $resource['attributes']);
        $this->assertArrayNotHasKey('status', $resource['attributes']);
    }

    #[Test]
    public function no_fieldsets_returns_all_attributes(): void
    {
        $entity = (object) ['id' => '1', 'title' => 'Hi', 'status' => 'draft'];
        $params = new QueryParameters;

        $resource = (new TestResourceTransformer)->toResource($entity, $params);

        $this->assertArrayHasKey('title', $resource['attributes']);
        $this->assertArrayHasKey('status', $resource['attributes']);
    }

    #[Test]
    public function relationships_appear_in_resource(): void
    {
        $entity = (object) ['id' => '1', 'title' => 'X', 'status' => 'draft'];
        $transformer = new TestResourceWithRelsTransformer;

        $resource = $transformer->toResource($entity);

        $this->assertArrayHasKey('relationships', $resource);
        $this->assertArrayHasKey('parent', $resource['relationships']);
        $this->assertSame('parents', $resource['relationships']['parent']['data']['type']);
        $this->assertSame('99', $resource['relationships']['parent']['data']['id']);
        $this->assertArrayNotHasKey('entity', $resource['relationships']['parent']);
        $this->assertArrayNotHasKey('transformer', $resource['relationships']['parent']);
    }

    #[Test]
    public function collect_includes_returns_empty_without_matches(): void
    {
        $entity = (object) ['id' => '1', 'title' => 'X', 'status' => 'draft'];
        $params = new QueryParameters(include: ['nonexistent']);

        $includes = (new TestResourceTransformer)->collectIncludes($entity, $params);

        $this->assertSame([], $includes);
    }

    #[Test]
    public function empty_relationships_omits_key(): void
    {
        $entity = (object) ['id' => '1', 'title' => 'X', 'status' => 'draft'];
        $resource = (new TestResourceTransformer)->toResource($entity);

        $this->assertArrayNotHasKey('relationships', $resource);
    }
}

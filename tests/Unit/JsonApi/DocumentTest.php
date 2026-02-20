<?php

declare(strict_types=1);

namespace Tests\Unit\JsonApi;

use App\JsonApi\Document;
use App\JsonApi\ErrorObject;
use App\JsonApi\Pagination;
use App\JsonApi\QueryParameters;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Unit\JsonApi\Stubs\StubTransformer;

class DocumentTest extends TestCase
{
    #[Test]
    public function singleDocumentStructure(): void
    {
        $entity = new \stdClass();
        $entity->id = '1';
        $entity->name = 'Test';

        $transformer = new StubTransformer();
        $doc = Document::single($transformer, $entity);

        $this->assertArrayHasKey('jsonapi', $doc);
        $this->assertSame('1.1', $doc['jsonapi']['version']);
        $this->assertArrayHasKey('data', $doc);
        $this->assertSame('stubs', $doc['data']['type']);
        $this->assertSame('1', $doc['data']['id']);
        $this->assertArrayHasKey('attributes', $doc['data']);
    }

    #[Test]
    public function singleDocumentWithSparseFieldsets(): void
    {
        $entity = new \stdClass();
        $entity->id = '1';
        $entity->name = 'Test';

        $params = new QueryParameters(fields: ['stubs' => ['name']]);
        $transformer = new StubTransformer();
        $doc = Document::single($transformer, $entity, $params);

        $this->assertArrayHasKey('name', $doc['data']['attributes']);
        $this->assertArrayNotHasKey('extra', $doc['data']['attributes']);
    }

    #[Test]
    public function collectionDocumentStructure(): void
    {
        $e1 = new \stdClass();
        $e1->id = '1';
        $e1->name = 'First';

        $e2 = new \stdClass();
        $e2->id = '2';
        $e2->name = 'Second';

        $transformer = new StubTransformer();
        $doc = Document::collection($transformer, [$e1, $e2]);

        $this->assertSame('1.1', $doc['jsonapi']['version']);
        $this->assertCount(2, $doc['data']);
        $this->assertSame('1', $doc['data'][0]['id']);
        $this->assertSame('2', $doc['data'][1]['id']);
    }

    #[Test]
    public function collectionDocumentWithPagination(): void
    {
        $e1 = new \stdClass();
        $e1->id = '1';
        $e1->name = 'Only';

        $pagination = new Pagination(total: 50, currentPage: 2, perPage: 10, baseUrl: '/api/v1/stubs');
        $doc = Document::collection(new StubTransformer(), [$e1], pagination: $pagination);

        $this->assertArrayHasKey('meta', $doc);
        $this->assertSame(50, $doc['meta']['page']['total']);
        $this->assertArrayHasKey('links', $doc);
        $this->assertArrayHasKey('first', $doc['links']);
    }

    #[Test]
    public function errorsDocumentStructure(): void
    {
        $doc = Document::errors(
            new ErrorObject('404', 'Not Found', 'Resource missing'),
            new ErrorObject('422', 'Validation Error', 'Name required'),
        );

        $this->assertSame('1.1', $doc['jsonapi']['version']);
        $this->assertCount(2, $doc['errors']);
        $this->assertSame('404', $doc['errors'][0]['status']);
        $this->assertSame('422', $doc['errors'][1]['status']);
    }

    #[Test]
    public function metaDocumentStructure(): void
    {
        $doc = Document::meta(['message' => 'Deleted', 'count' => 5]);

        $this->assertSame('1.1', $doc['jsonapi']['version']);
        $this->assertSame('Deleted', $doc['meta']['message']);
        $this->assertSame(5, $doc['meta']['count']);
        $this->assertArrayNotHasKey('data', $doc);
    }

    #[Test]
    public function collectionEmptyArray(): void
    {
        $doc = Document::collection(new StubTransformer(), []);

        $this->assertSame([], $doc['data']);
    }

    #[Test]
    public function singleDocumentHasLinks(): void
    {
        $entity = new \stdClass();
        $entity->id = '42';
        $entity->name = 'Linked';

        $doc = Document::single(new StubTransformer(), $entity);

        $this->assertArrayHasKey('links', $doc['data']);
        $this->assertSame('/stubs/42', $doc['data']['links']['self']);
    }
}

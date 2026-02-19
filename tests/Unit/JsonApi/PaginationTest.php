<?php

declare(strict_types=1);

namespace Tests\Unit\JsonApi;

use App\JsonApi\Pagination;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PaginationTest extends TestCase
{
    #[Test]
    public function lastPageCalculatedCorrectly(): void
    {
        $p = new Pagination(total: 100, currentPage: 1, perPage: 25, baseUrl: '/api/v1/recipes');
        $this->assertSame(4, $p->lastPage());
    }

    #[Test]
    public function lastPageRoundsUp(): void
    {
        $p = new Pagination(total: 101, currentPage: 1, perPage: 25, baseUrl: '/');
        $this->assertSame(5, $p->lastPage());
    }

    #[Test]
    public function lastPageMinimumIs1WhenEmpty(): void
    {
        $p = new Pagination(total: 0, currentPage: 1, perPage: 25, baseUrl: '/');
        $this->assertSame(1, $p->lastPage());
    }

    #[Test]
    public function fromAndToForFirstPage(): void
    {
        $p = new Pagination(total: 50, currentPage: 1, perPage: 10, baseUrl: '/');

        $this->assertSame(1, $p->from());
        $this->assertSame(10, $p->to());
    }

    #[Test]
    public function fromAndToForMiddlePage(): void
    {
        $p = new Pagination(total: 50, currentPage: 3, perPage: 10, baseUrl: '/');

        $this->assertSame(21, $p->from());
        $this->assertSame(30, $p->to());
    }

    #[Test]
    public function fromAndToForLastPagePartial(): void
    {
        $p = new Pagination(total: 53, currentPage: 6, perPage: 10, baseUrl: '/');

        $this->assertSame(51, $p->from());
        $this->assertSame(53, $p->to());
    }

    #[Test]
    public function fromIsZeroWhenEmpty(): void
    {
        $p = new Pagination(total: 0, currentPage: 1, perPage: 10, baseUrl: '/');
        $this->assertSame(0, $p->from());
    }

    #[Test]
    public function offsetCalculation(): void
    {
        $p = new Pagination(total: 100, currentPage: 3, perPage: 25, baseUrl: '/');
        $this->assertSame(50, $p->offset());
    }

    #[Test]
    public function toMetaStructure(): void
    {
        $p = new Pagination(total: 50, currentPage: 2, perPage: 10, baseUrl: '/');
        $meta = $p->toMeta();

        $this->assertArrayHasKey('page', $meta);
        $this->assertSame(2, $meta['page']['current-page']);
        $this->assertSame(10, $meta['page']['per-page']);
        $this->assertSame(11, $meta['page']['from']);
        $this->assertSame(20, $meta['page']['to']);
        $this->assertSame(50, $meta['page']['total']);
        $this->assertSame(5, $meta['page']['last-page']);
    }

    #[Test]
    public function toLinksFirstPage(): void
    {
        $p = new Pagination(total: 50, currentPage: 1, perPage: 10, baseUrl: '/api/v1/recipes');
        $links = $p->toLinks();

        $this->assertStringContainsString('page[number]=1', $links['first']);
        $this->assertStringContainsString('page[number]=5', $links['last']);
        $this->assertArrayNotHasKey('prev', $links);
        $this->assertArrayHasKey('next', $links);
        $this->assertStringContainsString('page[number]=2', $links['next']);
    }

    #[Test]
    public function toLinksLastPage(): void
    {
        $p = new Pagination(total: 50, currentPage: 5, perPage: 10, baseUrl: '/api/v1/recipes');
        $links = $p->toLinks();

        $this->assertArrayHasKey('prev', $links);
        $this->assertStringContainsString('page[number]=4', $links['prev']);
        $this->assertArrayNotHasKey('next', $links);
    }

    #[Test]
    public function toLinksMiddlePage(): void
    {
        $p = new Pagination(total: 50, currentPage: 3, perPage: 10, baseUrl: '/api/v1/recipes');
        $links = $p->toLinks();

        $this->assertArrayHasKey('prev', $links);
        $this->assertArrayHasKey('next', $links);
    }

    #[Test]
    public function toLinksWithQueryStringInBaseUrl(): void
    {
        $baseUrl = '/api/v1/recipes?filter[status]=published';
        $p = new Pagination(total: 50, currentPage: 1, perPage: 10, baseUrl: $baseUrl);
        $links = $p->toLinks();

        $this->assertStringContainsString('&page[number]=1', $links['first']);
    }

    #[Test]
    public function singlePageHasNoPrevOrNext(): void
    {
        $p = new Pagination(total: 5, currentPage: 1, perPage: 10, baseUrl: '/');
        $links = $p->toLinks();

        $this->assertArrayNotHasKey('prev', $links);
        $this->assertArrayNotHasKey('next', $links);
    }
}

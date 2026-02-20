<?php

declare(strict_types=1);

namespace Tests\Unit\JsonApi;

use App\JsonApi\Pagination;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PaginationTest extends TestCase
{
    #[Test]
    public function last_page_calculated_correctly(): void
    {
        $p = new Pagination(total: 100, currentPage: 1, perPage: 25, baseUrl: '/api/v1/recipes');
        $this->assertSame(4, $p->lastPage());
    }

    #[Test]
    public function last_page_rounds_up(): void
    {
        $p = new Pagination(total: 101, currentPage: 1, perPage: 25, baseUrl: '/');
        $this->assertSame(5, $p->lastPage());
    }

    #[Test]
    public function last_page_minimum_is1_when_empty(): void
    {
        $p = new Pagination(total: 0, currentPage: 1, perPage: 25, baseUrl: '/');
        $this->assertSame(1, $p->lastPage());
    }

    #[Test]
    public function from_and_to_for_first_page(): void
    {
        $p = new Pagination(total: 50, currentPage: 1, perPage: 10, baseUrl: '/');

        $this->assertSame(1, $p->from());
        $this->assertSame(10, $p->to());
    }

    #[Test]
    public function from_and_to_for_middle_page(): void
    {
        $p = new Pagination(total: 50, currentPage: 3, perPage: 10, baseUrl: '/');

        $this->assertSame(21, $p->from());
        $this->assertSame(30, $p->to());
    }

    #[Test]
    public function from_and_to_for_last_page_partial(): void
    {
        $p = new Pagination(total: 53, currentPage: 6, perPage: 10, baseUrl: '/');

        $this->assertSame(51, $p->from());
        $this->assertSame(53, $p->to());
    }

    #[Test]
    public function from_is_zero_when_empty(): void
    {
        $p = new Pagination(total: 0, currentPage: 1, perPage: 10, baseUrl: '/');
        $this->assertSame(0, $p->from());
    }

    #[Test]
    public function offset_calculation(): void
    {
        $p = new Pagination(total: 100, currentPage: 3, perPage: 25, baseUrl: '/');
        $this->assertSame(50, $p->offset());
    }

    #[Test]
    public function to_meta_structure(): void
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
    public function to_links_first_page(): void
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
    public function to_links_last_page(): void
    {
        $p = new Pagination(total: 50, currentPage: 5, perPage: 10, baseUrl: '/api/v1/recipes');
        $links = $p->toLinks();

        $this->assertArrayHasKey('prev', $links);
        $this->assertStringContainsString('page[number]=4', $links['prev']);
        $this->assertArrayNotHasKey('next', $links);
    }

    #[Test]
    public function to_links_middle_page(): void
    {
        $p = new Pagination(total: 50, currentPage: 3, perPage: 10, baseUrl: '/api/v1/recipes');
        $links = $p->toLinks();

        $this->assertArrayHasKey('prev', $links);
        $this->assertArrayHasKey('next', $links);
    }

    #[Test]
    public function to_links_with_query_string_in_base_url(): void
    {
        $baseUrl = '/api/v1/recipes?filter[status]=published';
        $p = new Pagination(total: 50, currentPage: 1, perPage: 10, baseUrl: $baseUrl);
        $links = $p->toLinks();

        $this->assertStringContainsString('&page[number]=1', $links['first']);
    }

    #[Test]
    public function single_page_has_no_prev_or_next(): void
    {
        $p = new Pagination(total: 5, currentPage: 1, perPage: 10, baseUrl: '/');
        $links = $p->toLinks();

        $this->assertArrayNotHasKey('prev', $links);
        $this->assertArrayNotHasKey('next', $links);
    }
}

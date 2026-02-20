<?php

declare(strict_types=1);

namespace Tests\Unit\JsonApi;

use App\JsonApi\QueryParameters;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class QueryParametersTest extends TestCase
{
    #[Test]
    public function defaults(): void
    {
        $params = new QueryParameters;

        $this->assertSame([], $params->filters);
        $this->assertSame([], $params->sort);
        $this->assertSame([], $params->include);
        $this->assertSame([], $params->fields);
        $this->assertSame(1, $params->pageNumber);
        $this->assertSame(25, $params->pageSize);
    }

    #[Test]
    public function from_array_parses_filters(): void
    {
        $params = QueryParameters::fromArray([
            'filter' => ['status' => 'published', 'difficulty' => 'easy'],
        ]);

        $this->assertTrue($params->hasFilter('status'));
        $this->assertSame('published', $params->getFilter('status'));
        $this->assertTrue($params->hasFilter('difficulty'));
        $this->assertSame('easy', $params->getFilter('difficulty'));
        $this->assertFalse($params->hasFilter('nonexistent'));
        $this->assertNull($params->getFilter('nonexistent'));
        $this->assertSame('fallback', $params->getFilter('nonexistent', 'fallback'));
    }

    #[Test]
    public function from_array_parses_sort(): void
    {
        $params = QueryParameters::fromArray([
            'sort' => '-created-at,title',
        ]);

        $this->assertCount(2, $params->sort);
        $this->assertSame('created-at', $params->sort[0]->field);
        $this->assertTrue($params->sort[0]->isDescending());
        $this->assertSame('title', $params->sort[1]->field);
        $this->assertFalse($params->sort[1]->isDescending());
    }

    #[Test]
    public function from_array_parses_include(): void
    {
        $params = QueryParameters::fromArray([
            'include' => 'author,cuisine,dish-type',
        ]);

        $this->assertSame(['author', 'cuisine', 'dish-type'], $params->include);
        $this->assertTrue($params->hasInclude('author'));
        $this->assertTrue($params->hasInclude('cuisine'));
        $this->assertFalse($params->hasInclude('ratings'));
    }

    #[Test]
    public function from_array_parses_sparse_fieldsets(): void
    {
        $params = QueryParameters::fromArray([
            'fields' => [
                'recipes' => 'title,description',
                'authors' => 'name',
            ],
        ]);

        $this->assertSame(['title', 'description'], $params->getFieldsFor('recipes'));
        $this->assertSame(['name'], $params->getFieldsFor('authors'));
        $this->assertNull($params->getFieldsFor('cuisines'));
    }

    #[Test]
    public function from_array_parses_pagination(): void
    {
        $params = QueryParameters::fromArray([
            'page' => ['number' => '3', 'size' => '10'],
        ]);

        $this->assertSame(3, $params->pageNumber);
        $this->assertSame(10, $params->pageSize);
    }

    #[Test]
    public function page_size_clamped_to_max100(): void
    {
        $params = QueryParameters::fromArray([
            'page' => ['size' => '500'],
        ]);

        $this->assertSame(100, $params->pageSize);
    }

    #[Test]
    public function page_number_minimum_is1(): void
    {
        $params = QueryParameters::fromArray([
            'page' => ['number' => '-5'],
        ]);

        $this->assertSame(1, $params->pageNumber);
    }

    #[Test]
    public function page_size_minimum_is1(): void
    {
        $params = QueryParameters::fromArray([
            'page' => ['size' => '0'],
        ]);

        $this->assertSame(1, $params->pageSize);
    }

    #[Test]
    public function empty_filter_values_are_excluded(): void
    {
        $params = QueryParameters::fromArray([
            'filter' => ['status' => 'published', 'empty' => ''],
        ]);

        $this->assertTrue($params->hasFilter('status'));
        $this->assertFalse($params->hasFilter('empty'));
    }

    #[Test]
    public function empty_sort_string_returns_empty(): void
    {
        $params = QueryParameters::fromArray(['sort' => '']);
        $this->assertSame([], $params->sort);
    }

    #[Test]
    public function empty_include_string_returns_empty(): void
    {
        $params = QueryParameters::fromArray(['include' => '']);
        $this->assertSame([], $params->include);
    }

    #[Test]
    public function non_array_filter_returns_empty(): void
    {
        $params = QueryParameters::fromArray(['filter' => 'not-an-array']);
        $this->assertSame([], $params->filters);
    }

    #[Test]
    public function non_string_sort_returns_empty(): void
    {
        $params = QueryParameters::fromArray(['sort' => 123]);
        $this->assertSame([], $params->sort);
    }

    #[Test]
    public function non_array_page_returns_defaults(): void
    {
        $params = QueryParameters::fromArray(['page' => 'string']);
        $this->assertSame(1, $params->pageNumber);
        $this->assertSame(25, $params->pageSize);
    }

    #[Test]
    public function non_array_fields_returns_empty(): void
    {
        $params = QueryParameters::fromArray(['fields' => 'string']);
        $this->assertSame([], $params->fields);
    }
}

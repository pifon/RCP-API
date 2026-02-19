<?php

declare(strict_types=1);

namespace App\JsonApi;

final class QueryParameters
{
    private const DEFAULT_PAGE_SIZE = 25;
    private const MAX_PAGE_SIZE = 100;

    /**
     * @param array<string, string> $filters
     * @param SortField[] $sort
     * @param string[] $include
     * @param array<string, string[]> $fields
     */
    public function __construct(
        public readonly array $filters = [],
        public readonly array $sort = [],
        public readonly array $include = [],
        public readonly array $fields = [],
        public readonly int $pageNumber = 1,
        public readonly int $pageSize = self::DEFAULT_PAGE_SIZE,
    ) {
    }

    /**
     * @param array<string, mixed> $query Raw query parameters ($_GET or similar)
     */
    public static function fromArray(array $query): self
    {
        $filters = self::parseFilters($query['filter'] ?? []);
        $sort = self::parseSort($query['sort'] ?? '');
        $include = self::parseInclude($query['include'] ?? '');
        $fields = self::parseFields($query['fields'] ?? []);
        $page = self::parsePage($query['page'] ?? []);

        return new self(
            filters: $filters,
            sort: $sort,
            include: $include,
            fields: $fields,
            pageNumber: $page['number'],
            pageSize: $page['size'],
        );
    }

    /**
     * @return string[]|null Fields for the given type, or null if not restricted
     */
    public function getFieldsFor(string $type): ?array
    {
        return $this->fields[$type] ?? null;
    }

    public function hasInclude(string $name): bool
    {
        return in_array($name, $this->include, true);
    }

    public function hasFilter(string $key): bool
    {
        return array_key_exists($key, $this->filters);
    }

    public function getFilter(string $key, ?string $default = null): ?string
    {
        return $this->filters[$key] ?? $default;
    }

    /**
     * @return array<string, string>
     */
    private static function parseFilters(mixed $raw): array
    {
        if (!is_array($raw)) {
            return [];
        }

        return array_filter(
            array_map('strval', $raw),
            fn (string $v) => $v !== '',
        );
    }

    /**
     * @return SortField[]
     */
    private static function parseSort(mixed $raw): array
    {
        if (!is_string($raw) || $raw === '') {
            return [];
        }

        return array_map(
            fn (string $v) => SortField::fromString(trim($v)),
            explode(',', $raw),
        );
    }

    /**
     * @return string[]
     */
    private static function parseInclude(mixed $raw): array
    {
        if (!is_string($raw) || $raw === '') {
            return [];
        }

        return array_map('trim', explode(',', $raw));
    }

    /**
     * @return array<string, string[]>
     */
    private static function parseFields(mixed $raw): array
    {
        if (!is_array($raw)) {
            return [];
        }

        $fields = [];
        foreach ($raw as $type => $fieldList) {
            if (is_string($fieldList) && $fieldList !== '') {
                $fields[(string) $type] = array_map('trim', explode(',', $fieldList));
            }
        }

        return $fields;
    }

    /**
     * @return array{number: int, size: int}
     */
    private static function parsePage(mixed $raw): array
    {
        if (!is_array($raw)) {
            return ['number' => 1, 'size' => self::DEFAULT_PAGE_SIZE];
        }

        $number = max(1, (int) ($raw['number'] ?? 1));
        $size = min(self::MAX_PAGE_SIZE, max(1, (int) ($raw['size'] ?? self::DEFAULT_PAGE_SIZE)));

        return ['number' => $number, 'size' => $size];
    }
}

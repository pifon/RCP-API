<?php

declare(strict_types=1);

namespace App\JsonApi;

final class SortField
{
    public function __construct(
        public readonly string $field,
        public readonly string $direction = 'asc',
    ) {}

    public static function fromString(string $value): self
    {
        if (str_starts_with($value, '-')) {
            return new self(field: substr($value, 1), direction: 'desc');
        }

        return new self(field: $value, direction: 'asc');
    }

    public function isDescending(): bool
    {
        return $this->direction === 'desc';
    }
}

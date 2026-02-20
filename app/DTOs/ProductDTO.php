<?php

namespace App\DTOs;

class ProductDTO
{
    public function __construct(
        public readonly string $id, // Slug
        public readonly string $name // full name
    ) {}
}

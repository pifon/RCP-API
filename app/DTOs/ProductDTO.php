<?php

namespace App\DTOs;

class ProductDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $slug,
        public readonly string $description,
        public readonly bool $isVegan,
        public readonly bool $isVegetarian,
        public readonly bool $isHalal,
        public readonly bool $isKosher,
        public readonly bool $isAllergen
    ) {}
}

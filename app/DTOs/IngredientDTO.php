<?php

namespace App\DTOs;

class IngredientDTO
{
    public function __construct(
        public readonly ProductDTO $product,
        public readonly float $amount,
        public readonly MeasureDTO $measure,
        /** @var IngredientNoteDTO[] */
        public readonly array $notes,
    ) {}
}

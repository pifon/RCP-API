<?php

namespace App\DTOs;

class IngredientDTO
{
    public function __construct(
        public readonly int $id,
        public readonly int $recipeId,
        public readonly ProductDTO $product,
        public readonly MeasureDTO $measure,
        public readonly float $amount,
        /** @var IngredientNoteDTO[] */
        public readonly array $notes,
    ) {}
}

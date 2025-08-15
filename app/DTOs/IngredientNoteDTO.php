<?php

namespace App\DTOs;

class IngredientNoteDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $note,
    ) {}
}

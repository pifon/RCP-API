<?php

namespace App\DTOs;

class IngredientNoteDTO
{
    public function __construct(
        public readonly string $note,
    ) {}
}

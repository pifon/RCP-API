<?php

namespace App\DTOs;

class MeasureDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $symbol,
    ) {}
}

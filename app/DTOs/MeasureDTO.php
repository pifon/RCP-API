<?php

namespace App\DTOs;

class MeasureDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $symbol,
        public readonly string $type,
        public readonly ?MeasureDTO $baseMeasure,
        public readonly bool $isBaseMeasure,
        public readonly float $factor
    ) {}
}

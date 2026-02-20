<?php

declare(strict_types=1);

namespace Tests\Helpers;

trait CreatesTestRecipe
{
    private const int TEST_CUISINE_ID = 1;

    /**
     * @return array<string, mixed> JSON:API relationships block with cuisine.
     */
    protected function cuisineRelationship(int $cuisineId = self::TEST_CUISINE_ID): array
    {
        return [
            'cuisine' => [
                'data' => ['type' => 'cuisines', 'id' => $cuisineId],
            ],
        ];
    }
}

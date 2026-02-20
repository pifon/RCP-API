<?php

declare(strict_types=1);

namespace Tests\Unit\Entities;

use App\Entities\Ingredient;
use App\Entities\Recipe;
use App\Entities\Serving;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class IngredientTest extends TestCase
{
    #[Test]
    public function constructor_defaults(): void
    {
        $ingredient = new Ingredient;

        $this->assertSame(0, $ingredient->getPosition());
        $this->assertCount(0, $ingredient->getNotes());
        $this->assertInstanceOf(\DateTime::class, $ingredient->getCreatedAt());
        $this->assertInstanceOf(\DateTime::class, $ingredient->getUpdatedAt());
    }

    #[Test]
    public function recipe_relation(): void
    {
        $ingredient = new Ingredient;
        $recipe = new Recipe;
        $recipe->setTitle('Test');
        $recipe->setSlug('test');

        $ingredient->setRecipe($recipe);
        $this->assertSame($recipe, $ingredient->getRecipe());
    }

    #[Test]
    public function position_setter(): void
    {
        $ingredient = new Ingredient;
        $ingredient->setPosition(3);
        $this->assertSame(3, $ingredient->getPosition());
    }

    #[Test]
    public function serving_relation(): void
    {
        $ingredient = new Ingredient;
        $serving = new Serving;

        $ingredient->setServing($serving);
        $this->assertSame($serving, $ingredient->getServing());
    }
}

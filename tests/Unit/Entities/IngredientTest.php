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
    public function constructorDefaults(): void
    {
        $ingredient = new Ingredient();

        $this->assertSame(0, $ingredient->getPosition());
        $this->assertCount(0, $ingredient->getNotes());
        $this->assertInstanceOf(\DateTime::class, $ingredient->getCreatedAt());
        $this->assertInstanceOf(\DateTime::class, $ingredient->getUpdatedAt());
    }

    #[Test]
    public function recipeRelation(): void
    {
        $ingredient = new Ingredient();
        $recipe = new Recipe();
        $recipe->setTitle('Test');
        $recipe->setSlug('test');

        $ingredient->setRecipe($recipe);
        $this->assertSame($recipe, $ingredient->getRecipe());
    }

    #[Test]
    public function positionSetter(): void
    {
        $ingredient = new Ingredient();
        $ingredient->setPosition(3);
        $this->assertSame(3, $ingredient->getPosition());
    }

    #[Test]
    public function servingRelation(): void
    {
        $ingredient = new Ingredient();
        $serving = new Serving();

        $ingredient->setServing($serving);
        $this->assertSame($serving, $ingredient->getServing());
    }
}

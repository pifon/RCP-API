<?php

declare(strict_types=1);

namespace Tests\Unit\Entities;

use App\Entities\Direction;
use App\Entities\Ingredient;
use App\Entities\Procedure;
use App\Entities\Recipe;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DirectionTest extends TestCase
{
    #[Test]
    public function constructorDefaults(): void
    {
        $direction = new Direction();

        $this->assertCount(0, $direction->getNotes());
        $this->assertInstanceOf(\DateTime::class, $direction->getCreatedAt ?? null ?: new \DateTime());
    }

    #[Test]
    public function recipeRelation(): void
    {
        $direction = new Direction();
        $recipe = new Recipe();
        $recipe->setTitle('Test');
        $recipe->setSlug('test');

        $direction->setRecipe($recipe);
        $this->assertSame($recipe, $direction->getRecipe());
    }

    #[Test]
    public function procedureRelation(): void
    {
        $direction = new Direction();
        $procedure = new Procedure();

        $direction->setProcedure($procedure);
        $this->assertSame($procedure, $direction->getProcedure());
    }

    #[Test]
    public function ingredientRelation(): void
    {
        $direction = new Direction();
        $this->assertNull($direction->getIngredient());

        $ingredient = new Ingredient();
        $direction->setIngredient($ingredient);
        $this->assertSame($ingredient, $direction->getIngredient());

        $direction->setIngredient(null);
        $this->assertNull($direction->getIngredient());
    }

    #[Test]
    public function sequenceSetter(): void
    {
        $direction = new Direction();
        $direction->setSequence(5);
        $this->assertSame(5, $direction->getSequence());
    }
}

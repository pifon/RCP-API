<?php

declare(strict_types=1);

namespace Tests\Unit\Entities;

use App\Entities\Cuisine;
use App\Entities\DishType;
use App\Entities\Recipe;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class RecipeTest extends TestCase
{
    #[Test]
    public function constructor_sets_defaults(): void
    {
        $recipe = new Recipe;

        $this->assertSame('draft', $recipe->getStatus());
        $this->assertNull($recipe->getPrepTimeMinutes());
        $this->assertNull($recipe->getCookTimeMinutes());
        $this->assertNull($recipe->getDifficulty());
        $this->assertNull($recipe->getServes());
        $this->assertNull($recipe->getPublishedAt());
        $this->assertNull($recipe->getSourceUrl());
        $this->assertNull($recipe->getPrice());
        $this->assertNull($recipe->getCurrency());
        $this->assertSame(0, $recipe->getForkRevenuePercent());
        $this->assertFalse($recipe->isDeleted());
        $this->assertTrue($recipe->isFree());
        $this->assertNull($recipe->getDeletedAt());
        $this->assertInstanceOf(\DateTime::class, $recipe->getCreatedAt());
        $this->assertInstanceOf(\DateTime::class, $recipe->getUpdatedAt());
        $this->assertCount(0, $recipe->getIngredients());
        $this->assertCount(0, $recipe->getDirections());
    }

    #[Test]
    public function title_and_slug(): void
    {
        $recipe = new Recipe;
        $recipe->setTitle('Pasta Carbonara');
        $recipe->setSlug('pasta-carbonara');

        $this->assertSame('Pasta Carbonara', $recipe->getTitle());
        $this->assertSame('Pasta Carbonara', $recipe->getName());
        $this->assertSame('pasta-carbonara', $recipe->getSlug());
        $this->assertSame('pasta-carbonara', $recipe->getIdentifier());
    }

    #[Test]
    public function total_time_both_set(): void
    {
        $recipe = new Recipe;
        $recipe->setPrepTimeMinutes(15);
        $recipe->setCookTimeMinutes(30);

        $this->assertSame(45, $recipe->getTotalTimeMinutes());
    }

    #[Test]
    public function total_time_only_prep(): void
    {
        $recipe = new Recipe;
        $recipe->setPrepTimeMinutes(10);

        $this->assertSame(10, $recipe->getTotalTimeMinutes());
    }

    #[Test]
    public function total_time_null_when_both_null(): void
    {
        $recipe = new Recipe;
        $this->assertNull($recipe->getTotalTimeMinutes());
    }

    #[Test]
    public function is_free_based_on_price(): void
    {
        $recipe = new Recipe;
        $this->assertTrue($recipe->isFree());

        $recipe->setPrice('9.99');
        $this->assertFalse($recipe->isFree());

        $recipe->setPrice(null);
        $this->assertTrue($recipe->isFree());
    }

    #[Test]
    public function soft_delete_and_restore(): void
    {
        $recipe = new Recipe;
        $this->assertFalse($recipe->isDeleted());

        $recipe->softDelete();
        $this->assertTrue($recipe->isDeleted());
        $this->assertNotNull($recipe->getDeletedAt());

        $recipe->restore();
        $this->assertFalse($recipe->isDeleted());
        $this->assertNull($recipe->getDeletedAt());
    }

    #[Test]
    public function status_workflow(): void
    {
        $recipe = new Recipe;
        $this->assertSame('draft', $recipe->getStatus());

        $recipe->setStatus('published');
        $this->assertSame('published', $recipe->getStatus());
    }

    #[Test]
    public function difficulty_setter(): void
    {
        $recipe = new Recipe;
        $recipe->setDifficulty('hard');
        $this->assertSame('hard', $recipe->getDifficulty());
    }

    #[Test]
    public function cuisine_relation(): void
    {
        $recipe = new Recipe;
        $this->assertNull($recipe->getCuisine());

        $cuisine = new Cuisine;
        $cuisine->setName('Italian');
        $recipe->setCuisine($cuisine);
        $this->assertSame($cuisine, $recipe->getCuisine());

        $recipe->setCuisine(null);
        $this->assertNull($recipe->getCuisine());
    }

    #[Test]
    public function dish_type_relation(): void
    {
        $recipe = new Recipe;
        $this->assertNull($recipe->getDishType());

        $dishType = new DishType('Main Course');
        $recipe->setDishType($dishType);
        $this->assertSame($dishType, $recipe->getDishType());
    }

    #[Test]
    public function forked_from_relation(): void
    {
        $original = new Recipe;
        $original->setTitle('Original');

        $fork = new Recipe;
        $fork->setForkedFrom($original);
        $this->assertSame($original, $fork->getForkedFrom());

        $fork->setForkedFrom(null);
        $this->assertNull($fork->getForkedFrom());
    }

    #[Test]
    public function variant_relation(): void
    {
        $base = new Recipe;
        $variant = new Recipe;
        $variant->setVariant($base);
        $this->assertSame($base, $variant->getVariant());
    }

    #[Test]
    public function source_fields(): void
    {
        $recipe = new Recipe;
        $recipe->setSourceUrl('https://example.com/recipe');
        $recipe->setSourceDescription('Found online');

        $this->assertSame('https://example.com/recipe', $recipe->getSourceUrl());
        $this->assertSame('Found online', $recipe->getSourceDescription());
    }

    #[Test]
    public function currency_and_fork_revenue(): void
    {
        $recipe = new Recipe;
        $recipe->setCurrency('GBP');
        $recipe->setForkRevenuePercent(15);

        $this->assertSame('GBP', $recipe->getCurrency());
        $this->assertSame(15, $recipe->getForkRevenuePercent());
    }

    #[Test]
    public function timestamps_can_be_set(): void
    {
        $recipe = new Recipe;
        $dt = new \DateTime('2025-01-01');
        $recipe->setCreatedAt($dt);
        $recipe->setUpdatedAt($dt);

        $this->assertSame($dt, $recipe->getCreatedAt());
        $this->assertSame($dt, $recipe->getUpdatedAt());
    }

    #[Test]
    public function set_created_at_defaults_to_now(): void
    {
        $recipe = new Recipe;
        $before = new \DateTime;
        $recipe->setCreatedAt();
        $after = new \DateTime;

        $this->assertGreaterThanOrEqual($before, $recipe->getCreatedAt());
        $this->assertLessThanOrEqual($after, $recipe->getCreatedAt());
    }
}

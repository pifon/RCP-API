<?php

declare(strict_types=1);

namespace Tests\Unit\Entities;

use App\Entities\Cuisine;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CuisineTest extends TestCase
{
    #[Test]
    public function full_name_without_variant(): void
    {
        $cuisine = new Cuisine;
        $cuisine->setName('Italian');
        $cuisine->setSlug('italian');

        $this->assertSame('Italian', $cuisine->getFullName());
        $this->assertSame('italian', $cuisine->getIdentifier());
    }

    #[Test]
    public function full_name_with_variant(): void
    {
        $cuisine = new Cuisine;
        $cuisine->setName('Italian');
        $cuisine->setVariant('Apulian');

        $this->assertSame('Italian - Apulian', $cuisine->getFullName());
    }

    #[Test]
    public function parent_relation(): void
    {
        $parent = new Cuisine;
        $parent->setName('European');

        $child = new Cuisine;
        $child->setName('Italian');
        $child->setParent($parent);

        $this->assertSame($parent, $child->getParent());
    }

    #[Test]
    public function description_setter(): void
    {
        $cuisine = new Cuisine;
        $cuisine->setDescription('Rich and diverse');
        $this->assertSame('Rich and diverse', $cuisine->getDescription());
    }
}

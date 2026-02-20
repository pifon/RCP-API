<?php

declare(strict_types=1);

namespace Tests\Unit\Entities;

use App\Entities\Measure;
use App\Entities\Product;
use App\Entities\Serving;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ServingTest extends TestCase
{
    #[Test]
    public function settersAndGetters(): void
    {
        $serving = new Serving();

        $product = $this->createMock(Product::class);
        $measure = $this->createMock(Measure::class);

        $serving->setProduct($product);
        $serving->setAmount(250.5);
        $serving->setMeasure($measure);

        $this->assertSame($product, $serving->getProduct());
        $this->assertSame(250.5, $serving->getAmount());
        $this->assertSame($measure, $serving->getMeasure());
    }
}

<?php

declare(strict_types=1);

namespace Tests\Unit\Entities;

use App\Entities\Operation;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class OperationTest extends TestCase
{
    #[Test]
    public function name_and_description(): void
    {
        $op = new Operation;
        $op->setName('sieve');
        $op->setDescription('Pass through a fine mesh');

        $this->assertSame('sieve', $op->getName());
        $this->assertSame('Pass through a fine mesh', $op->getDescription());
    }

    #[Test]
    public function description_defaults_to_null(): void
    {
        $op = new Operation;
        $op->setName('mix');

        $this->assertNull($op->getDescription());
    }
}

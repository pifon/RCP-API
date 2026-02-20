<?php

declare(strict_types=1);

namespace Tests\Unit\Entities;

use App\Entities\Operation;
use App\Entities\Procedure;
use App\Entities\Serving;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ProcedureTest extends TestCase
{
    #[Test]
    public function defaults(): void
    {
        $proc = new Procedure;

        $this->assertNull($proc->getServing());
        $this->assertNull($proc->getDuration());
    }

    #[Test]
    public function operation_relation(): void
    {
        $proc = new Procedure;
        $op = new Operation;
        $op->setName('boil');

        $proc->setOperation($op);
        $this->assertSame($op, $proc->getOperation());
    }

    #[Test]
    public function serving_relation(): void
    {
        $proc = new Procedure;
        $serving = new Serving;

        $proc->setServing($serving);
        $this->assertSame($serving, $proc->getServing());

        $proc->setServing(null);
        $this->assertNull($proc->getServing());
    }

    #[Test]
    public function duration_setter(): void
    {
        $proc = new Procedure;
        $proc->setDuration(10);
        $this->assertSame(10, $proc->getDuration());

        $proc->setDuration(null);
        $this->assertNull($proc->getDuration());
    }
}

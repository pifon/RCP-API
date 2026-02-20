<?php

declare(strict_types=1);

namespace Tests\Unit\Entities;

use App\Entities\Direction;
use App\Entities\DirectionNote;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DirectionNoteTest extends TestCase
{
    #[Test]
    public function setters_and_getters(): void
    {
        $note = new DirectionNote;
        $direction = new Direction;

        $note->setDirection($direction);
        $note->setNote('Season generously');

        $this->assertSame($direction, $note->getDirection());
        $this->assertSame('Season generously', $note->getNote());
    }
}

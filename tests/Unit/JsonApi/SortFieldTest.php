<?php

declare(strict_types=1);

namespace Tests\Unit\JsonApi;

use App\JsonApi\SortField;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class SortFieldTest extends TestCase
{
    #[Test]
    public function ascendingFieldFromString(): void
    {
        $field = SortField::fromString('title');

        $this->assertSame('title', $field->field);
        $this->assertSame('asc', $field->direction);
        $this->assertFalse($field->isDescending());
    }

    #[Test]
    public function descendingFieldFromString(): void
    {
        $field = SortField::fromString('-created-at');

        $this->assertSame('created-at', $field->field);
        $this->assertSame('desc', $field->direction);
        $this->assertTrue($field->isDescending());
    }

    #[Test]
    public function constructorDefaultsToAscending(): void
    {
        $field = new SortField('name');

        $this->assertSame('asc', $field->direction);
        $this->assertFalse($field->isDescending());
    }

    #[Test]
    public function constructorAcceptsExplicitDirection(): void
    {
        $field = new SortField('price', 'desc');

        $this->assertSame('price', $field->field);
        $this->assertTrue($field->isDescending());
    }
}

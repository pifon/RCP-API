<?php

declare(strict_types=1);

namespace Tests\Unit\JsonApi;

use App\JsonApi\SortField;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class SortFieldTest extends TestCase
{
    #[Test]
    public function ascending_field_from_string(): void
    {
        $field = SortField::fromString('title');

        $this->assertSame('title', $field->field);
        $this->assertSame('asc', $field->direction);
        $this->assertFalse($field->isDescending());
    }

    #[Test]
    public function descending_field_from_string(): void
    {
        $field = SortField::fromString('-created-at');

        $this->assertSame('created-at', $field->field);
        $this->assertSame('desc', $field->direction);
        $this->assertTrue($field->isDescending());
    }

    #[Test]
    public function constructor_defaults_to_ascending(): void
    {
        $field = new SortField('name');

        $this->assertSame('asc', $field->direction);
        $this->assertFalse($field->isDescending());
    }

    #[Test]
    public function constructor_accepts_explicit_direction(): void
    {
        $field = new SortField('price', 'desc');

        $this->assertSame('price', $field->field);
        $this->assertTrue($field->isDescending());
    }
}

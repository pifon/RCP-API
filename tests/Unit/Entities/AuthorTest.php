<?php

declare(strict_types=1);

namespace Tests\Unit\Entities;

use App\Entities\Author;
use App\Entities\User;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class AuthorTest extends TestCase
{
    #[Test]
    public function defaults(): void
    {
        $author = new Author();

        $this->assertSame('free', $author->getTier());
        $this->assertFalse($author->isDeleted());
        $this->assertCount(0, $author->getRecipes());
    }

    #[Test]
    public function nameAndEmail(): void
    {
        $author = new Author();
        $author->setName('John Doe');
        $author->setEmail('john@example.com');

        $this->assertSame('John Doe', $author->getName());
        $this->assertSame('john@example.com', $author->getEmail());
    }

    #[Test]
    public function tierSetter(): void
    {
        $author = new Author();
        $author->setTier('pro');
        $this->assertSame('pro', $author->getTier());
    }

    #[Test]
    public function softDeleteAndRestore(): void
    {
        $author = new Author();
        $this->assertFalse($author->isDeleted());

        $author->softDelete();
        $this->assertTrue($author->isDeleted());

        $author->restore();
        $this->assertFalse($author->isDeleted());
    }

    #[Test]
    public function descriptionSetter(): void
    {
        $author = new Author();
        $author->setDescription('A passionate chef');
        $this->assertSame('A passionate chef', $author->getDescription());
    }

    #[Test]
    public function userRelation(): void
    {
        $author = new Author();
        $user = $this->createMock(User::class);
        $user->method('getUsername')->willReturn('jdoe');

        $author->setUser($user);
        $this->assertSame($user, $author->getUser());
        $this->assertSame('jdoe', $author->getUsername());
        $this->assertSame('jdoe', $author->getIdentifier());
    }
}

<?php

declare(strict_types=1);

namespace Tests\Unit\Middleware;

use App\Entities\Author;
use App\Entities\User;
use App\Http\Middleware\CheckAuthorTier;
use App\Repositories\v1\AuthorRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CheckAuthorTierTest extends TestCase
{
    #[Test]
    public function passesWhenNoUser(): void
    {
        $repo = $this->createMock(AuthorRepository::class);
        $middleware = new CheckAuthorTier($repo);
        $request = Request::create('/api/v1/recipes', 'POST');

        $response = $middleware->handle($request, fn () => new Response('OK'), 'pro');

        $this->assertSame(200, $response->getStatusCode());
    }

    #[Test]
    public function passesWhenTierSufficient(): void
    {
        $user = $this->createMock(User::class);
        $author = $this->createMock(Author::class);
        $author->method('getTier')->willReturn('pro');

        $repo = $this->createMock(AuthorRepository::class);
        $repo->method('getAuthor')->willReturn($author);

        $middleware = new CheckAuthorTier($repo);
        $request = Request::create('/api/v1/recipes', 'POST');
        $request->setUserResolver(fn () => $user);

        $response = $middleware->handle($request, fn () => new Response('OK'), 'pro');

        $this->assertSame(200, $response->getStatusCode());
    }

    #[Test]
    public function passesWhenTierExceedsMinimum(): void
    {
        $user = $this->createMock(User::class);
        $author = $this->createMock(Author::class);
        $author->method('getTier')->willReturn('premium');

        $repo = $this->createMock(AuthorRepository::class);
        $repo->method('getAuthor')->willReturn($author);

        $middleware = new CheckAuthorTier($repo);
        $request = Request::create('/api/v1/recipes', 'POST');
        $request->setUserResolver(fn () => $user);

        $response = $middleware->handle($request, fn () => new Response('OK'), 'verified');

        $this->assertSame(200, $response->getStatusCode());
    }

    #[Test]
    public function blocksWhenTierInsufficient(): void
    {
        $user = $this->createMock(User::class);
        $author = $this->createMock(Author::class);
        $author->method('getTier')->willReturn('free');

        $repo = $this->createMock(AuthorRepository::class);
        $repo->method('getAuthor')->willReturn($author);

        $middleware = new CheckAuthorTier($repo);
        $request = Request::create('/api/v1/recipes', 'POST');
        $request->setUserResolver(fn () => $user);

        $response = $middleware->handle($request, fn () => new Response('OK'), 'pro');

        $this->assertSame(403, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);
        $this->assertSame('Insufficient Author Tier', $content['errors'][0]['title']);
    }

    #[Test]
    public function blocksWhenNoAuthorProfile(): void
    {
        $user = $this->createMock(User::class);

        $repo = $this->createMock(AuthorRepository::class);
        $repo->method('getAuthor')->willThrowException(new \RuntimeException('No author'));

        $middleware = new CheckAuthorTier($repo);
        $request = Request::create('/api/v1/recipes', 'POST');
        $request->setUserResolver(fn () => $user);

        $response = $middleware->handle($request, fn () => new Response('OK'), 'pro');

        $this->assertSame(403, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);
        $this->assertSame('Author Required', $content['errors'][0]['title']);
    }
}

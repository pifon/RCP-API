<?php

declare(strict_types=1);

namespace Tests\Unit\Middleware;

use App\Http\Middleware\ForceJsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ForceJsonResponseTest extends TestCase
{
    #[Test]
    public function sets_accept_header(): void
    {
        $middleware = new ForceJsonResponse;
        $request = Request::create('/api/v1/recipes', 'GET');

        $middleware->handle($request, function (Request $r) {
            return new Response('OK');
        });

        $this->assertSame('application/vnd.api+json', $request->headers->get('Accept'));
    }
}

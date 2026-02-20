<?php

declare(strict_types=1);

namespace Tests\Unit\Middleware;

use App\Http\Middleware\ValidateJsonApi;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ValidateJsonApiTest extends TestCase
{
    private ValidateJsonApi $middleware;

    protected function setUp(): void
    {
        parent::setUp();
        $this->middleware = new ValidateJsonApi();
    }

    #[Test]
    public function getRequestPassesWithoutContentType(): void
    {
        $request = Request::create('/api/v1/recipes', 'GET');

        $response = $this->middleware->handle($request, fn () => new Response('OK'));

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('application/vnd.api+json', $response->headers->get('Content-Type'));
    }

    #[Test]
    public function postWithCorrectContentTypePasses(): void
    {
        $request = Request::create('/api/v1/recipes', 'POST', [], [], [], [
            'CONTENT_TYPE' => 'application/vnd.api+json',
        ]);

        $response = $this->middleware->handle($request, fn () => new Response('Created', 201));

        $this->assertSame(201, $response->getStatusCode());
    }

    #[Test]
    public function postWithWrongContentTypeReturns415(): void
    {
        $request = Request::create('/api/v1/recipes', 'POST', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
        ]);

        $response = $this->middleware->handle($request, fn () => new Response('OK'));

        $this->assertSame(415, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);
        $this->assertSame('415', $content['errors'][0]['status']);
    }

    #[Test]
    public function patchWithWrongContentTypeReturns415(): void
    {
        $request = Request::create('/api/v1/me', 'PATCH', [], [], [], [
            'CONTENT_TYPE' => 'text/plain',
        ]);

        $response = $this->middleware->handle($request, fn () => new Response('OK'));

        $this->assertSame(415, $response->getStatusCode());
    }

    #[Test]
    public function deleteRequestPassesWithoutContentType(): void
    {
        $request = Request::create('/api/v1/follows/1', 'DELETE');

        $response = $this->middleware->handle($request, fn () => new Response('OK'));

        $this->assertSame(200, $response->getStatusCode());
    }

    #[Test]
    public function responseContentTypeIsAlwaysJsonapi(): void
    {
        $request = Request::create('/api/v1/recipes', 'GET');

        $response = $this->middleware->handle($request, fn () => new Response('{}'));

        $this->assertSame('application/vnd.api+json', $response->headers->get('Content-Type'));
    }
}

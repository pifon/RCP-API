<?php

declare(strict_types=1);

namespace Tests\Unit\Exceptions;

use App\Exceptions\v1\NotFoundException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class NotFoundExceptionTest extends TestCase
{
    #[Test]
    public function default_message_and_code(): void
    {
        $e = new NotFoundException;

        $this->assertSame('Resource not found', $e->getMessage());
        $this->assertSame(404, $e->getCode());
    }

    #[Test]
    public function custom_message(): void
    {
        $e = new NotFoundException("Recipe 'test' not found");

        $this->assertSame("Recipe 'test' not found", $e->getMessage());
        $this->assertSame(404, $e->getCode());
    }

    #[Test]
    public function render_returns_jsonapi_error(): void
    {
        $e = new NotFoundException('Not here');
        $response = $e->render();

        $this->assertSame(404, $response->getStatusCode());

        $body = json_decode($response->getContent(), true);
        $this->assertSame('1.1', $body['jsonapi']['version']);
        $this->assertSame('404', $body['errors'][0]['status']);
        $this->assertSame('Not Found', $body['errors'][0]['title']);
        $this->assertSame('Not here', $body['errors'][0]['detail']);
    }
}

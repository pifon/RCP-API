<?php

declare(strict_types=1);

namespace Tests\Unit\Exceptions;

use App\Exceptions\v1\ValidationErrorException;
use Illuminate\Support\MessageBag;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ValidationErrorExceptionTest extends TestCase
{
    #[Test]
    public function renderWithFieldErrors(): void
    {
        $e = new ValidationErrorException('Validation failed', [
            'title' => ['Title is required.'],
            'slug' => ['Slug already taken.', 'Slug too long.'],
        ]);

        $response = $e->render();

        $this->assertSame(422, $response->getStatusCode());

        $body = json_decode($response->getContent(), true);
        $this->assertSame('1.1', $body['jsonapi']['version']);
        $this->assertCount(3, $body['errors']);
        $this->assertSame('/data/attributes/title', $body['errors'][0]['source']['pointer']);
        $this->assertSame('Title is required.', $body['errors'][0]['detail']);
        $this->assertSame('/data/attributes/slug', $body['errors'][1]['source']['pointer']);
    }

    #[Test]
    public function renderWithEmptyErrorsUsesMessage(): void
    {
        $e = new ValidationErrorException('Something is wrong');

        $response = $e->render();

        $body = json_decode($response->getContent(), true);
        $this->assertCount(1, $body['errors']);
        $this->assertSame('Something is wrong', $body['errors'][0]['detail']);
    }

    #[Test]
    public function fromValidationBag(): void
    {
        $bag = new MessageBag([
            'email' => ['Must be a valid email.'],
            'password' => ['Too short.'],
        ]);

        $e = ValidationErrorException::fromValidationBag($bag);

        $this->assertSame('Validation Error', $e->getMessage());

        $response = $e->render();
        $body = json_decode($response->getContent(), true);
        $this->assertCount(2, $body['errors']);
    }

    #[Test]
    public function customStatusCode(): void
    {
        $e = new ValidationErrorException('Conflict', ['field' => ['Duplicate.']], 409);

        $response = $e->render();
        $this->assertSame(409, $response->getStatusCode());
    }
}

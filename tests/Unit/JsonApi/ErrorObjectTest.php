<?php

declare(strict_types=1);

namespace Tests\Unit\JsonApi;

use App\JsonApi\ErrorObject;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ErrorObjectTest extends TestCase
{
    #[Test]
    public function to_array_minimal(): void
    {
        $error = new ErrorObject('404', 'Not Found');

        $this->assertSame([
            'status' => '404',
            'title' => 'Not Found',
        ], $error->toArray());
    }

    #[Test]
    public function to_array_with_all_fields(): void
    {
        $error = new ErrorObject(
            status: '422',
            title: 'Validation Error',
            detail: 'Name is required.',
            code: 'VALIDATION_FAILED',
            source: ['pointer' => '/data/attributes/name'],
            meta: ['field' => 'name'],
        );

        $arr = $error->toArray();

        $this->assertSame('422', $arr['status']);
        $this->assertSame('Validation Error', $arr['title']);
        $this->assertSame('Name is required.', $arr['detail']);
        $this->assertSame('VALIDATION_FAILED', $arr['code']);
        $this->assertSame(['pointer' => '/data/attributes/name'], $arr['source']);
        $this->assertSame(['field' => 'name'], $arr['meta']);
    }

    #[Test]
    public function to_array_omits_null_fields(): void
    {
        $error = new ErrorObject('500', 'Internal Server Error', 'Something broke');

        $arr = $error->toArray();

        $this->assertArrayHasKey('detail', $arr);
        $this->assertArrayNotHasKey('code', $arr);
        $this->assertArrayNotHasKey('source', $arr);
        $this->assertArrayNotHasKey('meta', $arr);
    }

    #[Test]
    public function from_exception(): void
    {
        $exception = new \RuntimeException('Database timeout');
        $error = ErrorObject::fromException($exception, '503');

        $this->assertSame('503', $error->status);
        $this->assertSame('Error', $error->title);
        $this->assertSame('Database timeout', $error->detail);
    }

    #[Test]
    public function from_exception_with_known_status(): void
    {
        $exception = new \RuntimeException('Not allowed');
        $error = ErrorObject::fromException($exception, '403');

        $this->assertSame('Forbidden', $error->title);
    }

    #[Test]
    public function from_exception_with_empty_message(): void
    {
        $exception = new \RuntimeException('');
        $error = ErrorObject::fromException($exception);

        $this->assertSame('500', $error->status);
        $this->assertSame('Internal Server Error', $error->title);
        $this->assertNull($error->detail);
    }

    #[Test]
    public function validation_factory(): void
    {
        $error = ErrorObject::validation('email', 'Must be a valid email.');

        $this->assertSame('422', $error->status);
        $this->assertSame('Validation Error', $error->title);
        $this->assertSame('Must be a valid email.', $error->detail);
        $this->assertSame(['pointer' => '/data/attributes/email'], $error->source);
    }
}

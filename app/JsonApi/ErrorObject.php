<?php

declare(strict_types=1);

namespace App\JsonApi;

final class ErrorObject
{
    public function __construct(
        public readonly string $status,
        public readonly string $title,
        public readonly ?string $detail = null,
        public readonly ?string $code = null,
        public readonly ?array $source = null,
        public readonly ?array $meta = null,
    ) {
    }

    public function toArray(): array
    {
        $error = [
            'status' => $this->status,
            'title' => $this->title,
        ];

        if ($this->detail !== null) {
            $error['detail'] = $this->detail;
        }

        if ($this->code !== null) {
            $error['code'] = $this->code;
        }

        if ($this->source !== null) {
            $error['source'] = $this->source;
        }

        if ($this->meta !== null) {
            $error['meta'] = $this->meta;
        }

        return $error;
    }

    public static function fromException(\Throwable $e, string $status = '500'): self
    {
        return new self(
            status: $status,
            title: self::titleForStatus($status),
            detail: $e->getMessage() ?: null,
        );
    }

    public static function validation(string $field, string $detail): self
    {
        return new self(
            status: '422',
            title: 'Validation Error',
            detail: $detail,
            source: ['pointer' => "/data/attributes/$field"],
        );
    }

    private static function titleForStatus(string $status): string
    {
        return match ($status) {
            '400' => 'Bad Request',
            '401' => 'Unauthorized',
            '403' => 'Forbidden',
            '404' => 'Not Found',
            '405' => 'Method Not Allowed',
            '409' => 'Conflict',
            '415' => 'Unsupported Media Type',
            '422' => 'Unprocessable Entity',
            '429' => 'Too Many Requests',
            '500' => 'Internal Server Error',
            default => 'Error',
        };
    }
}

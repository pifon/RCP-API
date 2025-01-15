<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

abstract class ApiException extends Exception implements HttpExceptionInterface
{
    private string $description;
    private string $instance;
    private array $additionalData;
    private string $type;

    public function __construct(string $message, int $statusCode, ?string $type = null, ?string $description = null, ?string $instance = null, array $additionalData = [])
    {
        $this->message = $message;
        $this->code = $statusCode;
        $this->type = $type;
        $this->description = $description;
        $this->instance = $instance;
        $this->additionalData = $additionalData;

        parent::__construct($message, $statusCode, $additionalData['previous'] ?? null);
    }

    public function getDescription(): string
    {
        return $this->description;
    }
    public function getStatusCode(): int
    {
        return $this->code;
    }

    public function getDetails(): string
    {
        return $this->description;
    }
    public function getType(): ?string
    {
        return $this->type;
    }

    public function getInstance(): ?string
    {
        return $this->instance;
    }

    public function getAdditionalData(): array
    {
        return $this->additionalData;
    }

    public function getHeaders(): array
    {
        return [];
    }

    public function toArray(): array
    {
        return [
            'message' => $this->getMessage(),
            'type' => $this->getType(),
            'code' => $this->getCode(),
            'details' => $this->getDetails(),
            'instance' => $this->getInstance(),
            'additionalData' => $this->getAdditionalData(),
        ];
    }

    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    public function __toString(): string
    {
        return $this->toJson();
    }
}
<?php

declare(strict_types=1);

namespace App\Exceptions\v1;

use App\JsonApi\Document;
use App\JsonApi\ErrorObject;
use Exception;
use Illuminate\Http\JsonResponse;

class NotFoundException extends Exception
{
    public function __construct(string $message = 'Resource not found', int $status = 404)
    {
        parent::__construct($message, $status);
    }

    public function render(): JsonResponse
    {
        return response()->json(
            Document::errors(new ErrorObject(
                status: (string) $this->code,
                title: 'Not Found',
                detail: $this->getMessage(),
            )),
            $this->code,
            ['Content-Type' => 'application/vnd.api+json'],
        );
    }
}

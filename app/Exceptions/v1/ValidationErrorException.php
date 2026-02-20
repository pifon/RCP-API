<?php

declare(strict_types=1);

namespace App\Exceptions\v1;

use App\JsonApi\Document;
use App\JsonApi\ErrorObject;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\MessageBag;

class ValidationErrorException extends Exception
{
    /** @var array<string, string[]> */
    protected array $errors;

    /**
     * @param  array<string, string[]>  $errors
     */
    public function __construct(string $message, array $errors = [], int $status = 422)
    {
        parent::__construct($message, $status);
        $this->errors = $errors;
    }

    public function render(): JsonResponse
    {
        $errorObjects = [];
        foreach ($this->errors as $field => $messages) {
            foreach ($messages as $detail) {
                $errorObjects[] = new ErrorObject(
                    status: '422',
                    title: 'Validation Error',
                    detail: $detail,
                    source: ['pointer' => "/data/attributes/$field"],
                );
            }
        }

        if ($errorObjects === []) {
            $errorObjects[] = new ErrorObject(
                status: '422',
                title: 'Validation Error',
                detail: $this->getMessage(),
            );
        }

        return response()->json(
            Document::errors(...$errorObjects),
            $this->code,
            ['Content-Type' => 'application/vnd.api+json'],
        );
    }

    public static function fromValidationBag(MessageBag $errors): self
    {
        return new self('Validation Error', $errors->toArray());
    }
}

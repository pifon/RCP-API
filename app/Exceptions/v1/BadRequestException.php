<?php

declare(strict_types=1);

namespace App\Exceptions\v1;

use App\JsonApi\Document;
use App\JsonApi\ErrorObject;
use Exception;
use Illuminate\Http\JsonResponse;

class BadRequestException extends Exception
{
    /** @var string[] */
    protected array $errors;

    /**
     * @param string[] $errors
     */
    public function __construct(string $message, array $errors = [], int $status = 400)
    {
        parent::__construct($message, $status);
        $this->errors = $errors;
    }

    public function render(): JsonResponse
    {
        $errorObjects = [];
        if ($this->errors !== []) {
            foreach ($this->errors as $detail) {
                $errorObjects[] = new ErrorObject(
                    status: (string) $this->code,
                    title: 'Bad Request',
                    detail: $detail,
                );
            }
        } else {
            $errorObjects[] = new ErrorObject(
                status: (string) $this->code,
                title: 'Bad Request',
                detail: $this->getMessage(),
            );
        }

        return response()->json(
            Document::errors(...$errorObjects),
            $this->code,
            ['Content-Type' => 'application/vnd.api+json'],
        );
    }
}

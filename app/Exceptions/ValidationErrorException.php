<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class ValidationErrorException extends Exception
{
    protected array $errors;

    public function __construct(string $message, array $errors = [], int $status = 422)
    {
        parent::__construct($message, $status);
        $this->errors = $errors;
    }

    public function render()
    {
        return response()->json([
            'status' => $this->code,
            'message' => $this->message,
            'errors' => $this->errors,
        ], $this->code);
    }
}

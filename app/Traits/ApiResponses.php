<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponses
{
    protected function ok(string $message, $data): JsonResponse
    {
        return $this->success($message, $data, 200);
    }

    protected function success(string $message, $data, int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'message' => $message,
            'status' => $statusCode,
        ], $statusCode);
    }

    protected function error(string $message, int $statusCode): JsonResponse
    {
        return $this->success($message, [], $statusCode);
    }
}

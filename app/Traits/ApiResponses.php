<?php
declare(strict_types=1);

namespace App\Traits;
use Illuminate\Http\JsonResponse;

trait ApiResponses {

    /**
     * @param string $message
     * @return JsonResponse
     */
    protected function ok(string $message): JsonResponse
    {
        return $this->success($message, 200);
    }

    /**
     * @param string $message
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function success(string $message, int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'status' => $statusCode
        ], $statusCode);
    }
}
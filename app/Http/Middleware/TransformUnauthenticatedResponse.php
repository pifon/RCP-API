<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;

class TransformUnauthenticatedResponse
{
    public function handle($request, Closure $next)
    {

        $response = $next($request);

        // Only modify JSON responses
        if ($response instanceof JsonResponse) {
            $data = $response->getData(true);

            // Detect the {message: "Unauthenticated."} pattern
            if (isset($data['message']) && strtolower($data['message']) === 'unauthenticated.') {
                $data = [
                    'errors' => [[
                        'title' => 'Unauthenticated',
                        'detail' => 'You must login to access this resource',
                        'code' => '401',
                    ]],
                ];
                $response->setData($data);
                $response->setStatusCode(401);
            }
        }

        return $response;
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ApiLoginRequest;
use App\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;

/**
 * Handles authentication-related operations.
 */
class AuthController extends Controller
{
    use ApiResponses;

    /**
     * Log in a user and return a response.
     *
     * @param  ApiLoginRequest  $request  The validated login request.
     * @return JsonResponse The login response.
     */
    public function login(ApiLoginRequest $request): JsonResponse
    {
        return $this->ok('Hello, '.$request->get('email'));
    }

    /**
     * Handle user registration.
     *
     * @return JsonResponse The registration response.
     */
    public function register(): JsonResponse
    {
        return $this->ok('Register');
    }
}

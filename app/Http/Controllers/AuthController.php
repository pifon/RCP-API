<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApiLoginRequest;
use App\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use ApiResponses;
    public function login(ApiLoginRequest $request): JsonResponse
    {
        return $this->ok("Hello, " . $request->get('email'));
    }

    public function register(): JsonResponse
    {
        return $this->ok("Register");
    }
}

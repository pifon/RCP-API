<?php

namespace App\Http\Controllers;

use App\Exceptions\v1\BadRequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\JWTGuard;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:72',
        ], [
            'username.max' => trans('auth.username.max'),
            'password.max' => trans('auth.password.max'),
        ]);

        if (! empty(array_diff(array_keys($request->all()), ['username', 'password']))) {
            throw new BadRequestException(
                trans('auth.unexpected_fields.message'),
                [
                    trans('auth.unexpected_fields.error'),
                ]
            );
        }

        $credentials = $request->only('username', 'password');

        /** @var JWTGuard $jwtGuard */
        $jwtGuard = Auth::guard('api');

        if (! $token = $jwtGuard->attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        // TODO: Store token on user in token field
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $jwtGuard->factory()->getTTL() * 60,
        ]);
    }
}

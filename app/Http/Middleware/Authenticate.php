<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Exceptions\UnauthorizedException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    protected function redirectTo(Request $request): ?string
    {
        if (!$request->expectsJson()) {
            return route('login');
        }

        return null;
    }

    protected function unauthenticated($request, array $guards): void
    {
        if ($request->expectsJson()) {
            throw new UnauthorizedException("Unauthorised");
        }

        parent::unauthenticated($request, $guards);
    }
}

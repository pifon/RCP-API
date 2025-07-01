<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Exceptions\v1\UnauthorizedException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    protected function redirectTo(Request $request): ?string
    {
        if (! $request->expectsJson()) {
            return route('login');
        }

        return null;
    }

    /**
     * @param  Request  $request
     * @param  array<int, string>  $guards
     *
     * @throws UnauthorizedException
     * @throws AuthenticationException
     */
    protected function unauthenticated($request, array $guards): never
    {
        if ($request instanceof Request && $request->expectsJson()) {
            throw new UnauthorizedException('Unauthorised');
        }

        parent::unauthenticated($request, $guards);
    }
}

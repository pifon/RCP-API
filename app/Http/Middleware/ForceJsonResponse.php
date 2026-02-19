<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceJsonResponse
{
    private const CONTENT_TYPE = 'application/vnd.api+json';

    public function handle(Request $request, Closure $next): Response
    {
        $request->headers->set('Accept', self::CONTENT_TYPE);

        return $next($request);
    }
}

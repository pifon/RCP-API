<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\JsonApi\Document;
use App\JsonApi\ErrorObject;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateJsonApi
{
    private const CONTENT_TYPE = 'application/vnd.api+json';

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('POST') || $request->isMethod('PATCH')) {
            $contentType = $request->header('Content-Type', '');
            if (! str_contains($contentType, self::CONTENT_TYPE)) {
                return response()->json(
                    Document::errors(new ErrorObject(
                        status: '415',
                        title: 'Unsupported Media Type',
                        detail: 'Content-Type must be '.self::CONTENT_TYPE,
                    )),
                    415,
                    ['Content-Type' => self::CONTENT_TYPE],
                );
            }
        }

        $response = $next($request);

        $response->headers->set('Content-Type', self::CONTENT_TYPE);

        return $response;
    }
}

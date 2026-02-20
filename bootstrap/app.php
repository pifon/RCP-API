<?php

use App\JsonApi\Document;
use App\JsonApi\ErrorObject;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'feature' => \App\Http\Middleware\CheckFeature::class,
            'paid-recipe' => \App\Http\Middleware\CheckPaidRecipe::class,
            'author.tier' => \App\Http\Middleware\CheckAuthorTier::class,
            'tiered-rate' => \App\Http\Middleware\TieredRateLimit::class,
            'recipe-owner' => \App\Http\Middleware\RecipeOwner::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $isApi = fn (Request $r) => $r->is('api/*');

        $exceptions->render(function (AuthenticationException $e, Request $request) use ($isApi) {
            if (! $isApi($request)) {
                return null;
            }

            return response()->json(
                Document::errors(new ErrorObject(
                    status: '401',
                    title: 'Unauthorized',
                    detail: 'You must be authenticated to access this resource.',
                )),
                401,
                ['Content-Type' => 'application/vnd.api+json'],
            );
        });

        $exceptions->render(function (ValidationException $e, Request $request) use ($isApi) {
            if (! $isApi($request)) {
                return null;
            }
            $errors = [];
            foreach ($e->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $errors[] = new ErrorObject(
                        status: '422',
                        title: 'Validation Error',
                        detail: $message,
                        source: ['pointer' => "/data/attributes/$field"],
                    );
                }
            }

            return response()->json(
                Document::errors(...$errors),
                422,
                ['Content-Type' => 'application/vnd.api+json'],
            );
        });

        $exceptions->render(function (NotFoundHttpException $e, Request $request) use ($isApi) {
            if (! $isApi($request)) {
                return null;
            }

            return response()->json(
                Document::errors(new ErrorObject(
                    status: '404',
                    title: 'Not Found',
                    detail: $e->getMessage() ?: 'The requested resource was not found.',
                )),
                404,
                ['Content-Type' => 'application/vnd.api+json'],
            );
        });

        $exceptions->render(function (MethodNotAllowedHttpException $e, Request $request) use ($isApi) {
            if (! $isApi($request)) {
                return null;
            }

            return response()->json(
                Document::errors(new ErrorObject(
                    status: '405',
                    title: 'Method Not Allowed',
                    detail: $e->getMessage(),
                )),
                405,
                ['Content-Type' => 'application/vnd.api+json'],
            );
        });

        $exceptions->render(function (HttpExceptionInterface $e, Request $request) use ($isApi) {
            if (! $isApi($request)) {
                return null;
            }
            $status = (string) $e->getStatusCode();

            return response()->json(
                Document::errors(ErrorObject::fromException($e, $status)),
                $e->getStatusCode(),
                ['Content-Type' => 'application/vnd.api+json'],
            );
        });

        $exceptions->render(function (\Throwable $e, Request $request) use ($isApi) {
            if (! $isApi($request)) {
                return null;
            }
            $debug = config('app.debug');
            $error = new ErrorObject(
                status: '500',
                title: 'Internal Server Error',
                detail: $debug ? $e->getMessage() : 'An unexpected error occurred.',
                meta: $debug ? ['exception' => get_class($e), 'file' => $e->getFile(), 'line' => $e->getLine()] : null,
            );

            return response()->json(
                Document::errors($error),
                500,
                ['Content-Type' => 'application/vnd.api+json'],
            );
        });
    })->create();

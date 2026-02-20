<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\JsonApi\Document;
use App\JsonApi\ErrorObject;
use App\Services\FeatureGate;
use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;

class TieredRateLimit
{
    public function __construct(
        private readonly FeatureGate $featureGate,
        private readonly RateLimiter $limiter,
    ) {}

    public function handle(Request $request, Closure $next): mixed
    {
        $user = $request->user();
        if ($user === null) {
            $key = 'rate:ip:'.$request->ip();
            $maxAttempts = 30;
        } else {
            $key = 'rate:user:'.$user->getId();
            $maxAttempts = $this->featureGate->apiRateLimit($user);
        }

        if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
            $retryAfter = $this->limiter->availableIn($key);

            return response()->json(
                Document::errors(new ErrorObject(
                    status: '429',
                    title: 'Too Many Requests',
                    detail: "Rate limit exceeded. Try again in {$retryAfter} seconds.",
                    meta: ['retry-after' => $retryAfter],
                )),
                429,
                [
                    'Content-Type' => 'application/vnd.api+json',
                    'Retry-After' => (string) $retryAfter,
                    'X-RateLimit-Limit' => (string) $maxAttempts,
                    'X-RateLimit-Remaining' => '0',
                ],
            );
        }

        $this->limiter->hit($key, 60);

        $response = $next($request);

        return $response->withHeaders([
            'X-RateLimit-Limit' => (string) $maxAttempts,
            'X-RateLimit-Remaining' => (string) $this->limiter->remaining($key, $maxAttempts),
        ]);
    }
}

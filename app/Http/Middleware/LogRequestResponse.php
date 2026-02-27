<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogRequestResponse
{
    private const REDACT_KEYS = ['password', 'token', 'secret', 'authorization', 'cookie'];

    public function handle(Request $request, Closure $next): Response
    {
        if (! config('app.debug', false)) {
            return $next($request);
        }

        $this->logRequest($request);

        $response = $next($request);

        $this->logResponse($request, $response);

        return $response;
    }

    private function logRequest(Request $request): void
    {
        $payload = $this->capturePayload($request);
        $payload = $this->redactSensitive($payload);

        Log::info('API request', [
            'method' => $request->method(),
            'uri' => $request->getRequestUri(),
            'query' => $request->query->all(),
            'payload' => $payload,
        ]);
    }

    private function logResponse(Request $request, Response $response): void
    {
        $content = $response->getContent();
        if ($content !== false) {
            $decoded = json_decode($content, true);
            $body = $decoded !== null ? $decoded : $content;
        } else {
            $body = '(streamed)';
        }

        Log::info('API response', [
            'method' => $request->method(),
            'uri' => $request->getRequestUri(),
            'status' => $response->getStatusCode(),
            'body' => $body,
        ]);
    }

    /**
     * @return array<string, mixed>|string
     */
    private function capturePayload(Request $request): array|string
    {
        $content = $request->getContent();
        if ($content === '' || $content === false) {
            return $request->all();
        }

        $decoded = json_decode($content, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        return $content;
    }

    /**
     * @param  array<string, mixed>|string  $data
     * @return array<string, mixed>|string
     */
    private function redactSensitive(array|string $data): array|string
    {
        if (is_string($data)) {
            return $data;
        }

        $out = [];
        foreach ($data as $key => $value) {
            $lower = strtolower((string) $key);
            $redact = false;
            foreach (self::REDACT_KEYS as $needle) {
                if (str_contains($lower, $needle)) {
                    $redact = true;
                    break;
                }
            }
            if ($redact) {
                $out[$key] = '[REDACTED]';
                continue;
            }
            if (is_array($value)) {
                $out[$key] = $this->redactSensitive($value);
            } else {
                $out[$key] = $value;
            }
        }

        return $out;
    }
}

<?php

declare(strict_types=1);

namespace Tests\Helpers;

use Illuminate\Testing\TestResponse;

trait JsonApiRequests
{
    private const string JSONAPI_CT = 'application/vnd.api+json';

    abstract protected function getAuthToken(): string;

    protected function apiGet(string $uri): TestResponse
    {
        return $this->withHeaders([
            'Authorization' => 'Bearer '.$this->getAuthToken(),
            'Accept' => self::JSONAPI_CT,
        ])->getJson($uri);
    }

    protected function apiPost(string $uri, array $data = []): TestResponse
    {
        return $this->call(
            'POST',
            $uri,
            [],
            [],
            [],
            [
                'HTTP_AUTHORIZATION' => 'Bearer '.$this->getAuthToken(),
                'CONTENT_TYPE' => self::JSONAPI_CT,
                'HTTP_ACCEPT' => self::JSONAPI_CT,
            ],
            json_encode($data),
        );
    }

    protected function apiPatch(string $uri, array $data = []): TestResponse
    {
        return $this->call(
            'PATCH',
            $uri,
            [],
            [],
            [],
            [
                'HTTP_AUTHORIZATION' => 'Bearer '.$this->getAuthToken(),
                'CONTENT_TYPE' => self::JSONAPI_CT,
                'HTTP_ACCEPT' => self::JSONAPI_CT,
            ],
            json_encode($data),
        );
    }

    protected function apiDelete(string $uri): TestResponse
    {
        return $this->withHeaders([
            'Authorization' => 'Bearer '.$this->getAuthToken(),
            'Accept' => self::JSONAPI_CT,
        ])->deleteJson($uri);
    }
}

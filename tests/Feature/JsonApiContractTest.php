<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Entities\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Testing\TestResponse;
use Tests\Helpers\CreatesTestUser;
use Tests\TestCase;

class JsonApiContractTest extends TestCase
{
    use CreatesTestUser;
    use DatabaseTransactions;

    private const string CT = 'application/vnd.api+json';

    private User $user;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createOrGetTestUser();
        $this->token = auth('api')->login($this->user);
    }

    private function apiGet(string $uri): TestResponse
    {
        return $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
            'Accept' => self::CT,
        ])->getJson($uri);
    }

    private function apiPost(string $uri, array $data = []): TestResponse
    {
        return $this->call(
            'POST',
            $uri,
            [],
            [],
            [],
            [
                'HTTP_AUTHORIZATION' => "Bearer {$this->token}",
                'CONTENT_TYPE' => self::CT,
                'HTTP_ACCEPT' => self::CT,
            ],
            json_encode($data),
        );
    }

    private function apiPatch(string $uri, array $data = []): TestResponse
    {
        return $this->call(
            'PATCH',
            $uri,
            [],
            [],
            [],
            [
                'HTTP_AUTHORIZATION' => "Bearer {$this->token}",
                'CONTENT_TYPE' => self::CT,
                'HTTP_ACCEPT' => self::CT,
            ],
            json_encode($data),
        );
    }

    private function apiDelete(string $uri): TestResponse
    {
        return $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
            'Accept' => self::CT,
        ])->deleteJson($uri);
    }

    public function test_welcome_returns_jsonapi_meta(): void
    {
        $response = $this->getJson('/api');

        $response->assertStatus(200)
            ->assertJsonStructure(['jsonapi' => ['version'], 'meta'])
            ->assertJsonPath('jsonapi.version', '1.1');
    }

    public function test_unauthenticated_returns_jsonapi_error(): void
    {
        auth('api')->logout();

        $response = $this->call(
            'GET',
            '/api/v1/me',
            [],
            [],
            [],
            ['HTTP_ACCEPT' => self::CT],
        );

        $response->assertStatus(401)
            ->assertJsonStructure(['jsonapi', 'errors' => [['status', 'title', 'detail']]])
            ->assertJsonPath('errors.0.status', '401');
    }

    public function test_get_me_returns_user_resource(): void
    {
        $response = $this->apiGet('/api/v1/me');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'jsonapi',
                'data' => ['type', 'id', 'attributes', 'links'],
            ])
            ->assertJsonPath('data.type', 'users')
            ->assertJsonPath('data.id', $this->user->getUsername());
    }

    public function test_patch_me_updates_profile(): void
    {
        $response = $this->apiPatch('/api/v1/me', [
            'data' => [
                'type' => 'users',
                'attributes' => ['name' => 'Test Updated Name'],
            ],
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.attributes.name', 'Test Updated Name');
    }

    public function test_recipes_list_is_paginated(): void
    {
        $response = $this->apiGet('/api/v1/recipes?page[size]=2');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'jsonapi',
                'data',
                'meta' => ['page'],
                'links',
            ])
            ->assertJsonPath('meta.page.per-page', 2);
    }

    public function test404_returns_jsonapi_error(): void
    {
        $response = $this->apiGet('/api/v1/recipes/nonexistent-slug-xyz');

        $response->assertStatus(404)
            ->assertJsonStructure(['jsonapi', 'errors' => [['status', 'title', 'detail']]])
            ->assertJsonPath('errors.0.status', '404');
    }

    public function test_plans_list_returns_catalog(): void
    {
        $response = $this->apiGet('/api/v1/plans');

        $response->assertStatus(200)
            ->assertJsonStructure(['jsonapi', 'data']);
    }

    public function test_me_subscription_returns_valid_response(): void
    {
        $response = $this->apiGet('/api/v1/me/subscription');

        $response->assertStatus(200)
            ->assertJsonStructure(['jsonapi']);

        $json = $response->json();
        $hasSubscription = isset($json['data']);
        $hasFree = isset($json['meta']['plan']) && $json['meta']['plan'] === 'free';

        $this->assertTrue(
            $hasSubscription || $hasFree,
            'Expected either subscription data or free plan meta',
        );
    }

    public function test_preferences_returns_defaults(): void
    {
        $response = $this->apiGet('/api/v1/me/preferences');

        $response->assertStatus(200)
            ->assertJsonStructure(['jsonapi', 'data' => ['type', 'id', 'attributes']])
            ->assertJsonPath('data.type', 'user-preferences');
    }

    public function test_collections_crud_lifecycle(): void
    {
        $create = $this->apiPost('/api/v1/collections', [
            'data' => [
                'type' => 'collections',
                'attributes' => ['name' => 'Test Bag', 'type' => 'bag'],
            ],
        ]);

        $create->assertStatus(201)
            ->assertJsonPath('data.type', 'collections')
            ->assertJsonPath('data.attributes.name', 'Test Bag');

        $id = $create->json('data.id');

        $show = $this->apiGet("/api/v1/collections/{$id}");
        $show->assertStatus(200)->assertJsonPath('data.id', $id);

        $update = $this->apiPatch("/api/v1/collections/{$id}", [
            'data' => [
                'type' => 'collections',
                'attributes' => ['name' => 'Renamed Bag'],
            ],
        ]);
        $update->assertStatus(200)->assertJsonPath('data.attributes.name', 'Renamed Bag');

        $delete = $this->apiDelete("/api/v1/collections/{$id}");
        $delete->assertStatus(200)->assertJsonPath('meta.message', 'Collection deleted.');
    }

    public function test_follows_create_and_list(): void
    {
        $uniqueId = random_int(100000, 999999);

        $create = $this->apiPost('/api/v1/follows', [
            'data' => [
                'type' => 'follows',
                'attributes' => ['followable-type' => 'authors', 'followable-id' => $uniqueId],
            ],
        ]);

        $create->assertStatus(201)
            ->assertJsonPath('data.type', 'follows');

        $list = $this->apiGet('/api/v1/me/following');
        $list->assertStatus(200)->assertJsonStructure(['jsonapi', 'data', 'meta', 'links']);
    }

    public function test_register_creates_user(): void
    {
        $username = 'test-register-'.time();

        $response = $this->call(
            'POST',
            '/api/register',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => self::CT,
                'HTTP_ACCEPT' => self::CT,
            ],
            json_encode([
                'data' => [
                    'type' => 'users',
                    'attributes' => [
                        'username' => $username,
                        'name' => 'Reg Test',
                        'email' => "{$username}@example.com",
                        'password' => 'secret1234',
                    ],
                ],
            ]),
        );

        $response->assertStatus(201)
            ->assertJsonPath('data.type', 'users')
            ->assertJsonPath('data.id', $username);
    }
}

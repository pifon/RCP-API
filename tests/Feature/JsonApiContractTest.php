<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Entities\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Helpers\CreatesTestUser;
use Tests\TestCase;

class JsonApiContractTest extends TestCase
{
    use CreatesTestUser;
    use DatabaseTransactions;

    private User $user;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createOrGetTestUser();
        $this->token = auth('api')->login($this->user);
    }

    private function api(): \Illuminate\Testing\TestResponse
    {
        return $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
            'Accept' => 'application/vnd.api+json',
            'Content-Type' => 'application/vnd.api+json',
        ]);
    }

    public function testWelcomeReturnsJsonapiMeta(): void
    {
        $response = $this->getJson('/api');

        $response->assertStatus(200)
            ->assertJsonStructure(['jsonapi' => ['version'], 'meta'])
            ->assertJsonPath('jsonapi.version', '1.1');
    }

    public function testUnauthenticatedReturnsJsonapiError(): void
    {
        $response = $this->withHeader('Accept', 'application/vnd.api+json')
            ->getJson('/api/v1/me');

        $response->assertStatus(401)
            ->assertJsonStructure(['jsonapi', 'errors' => [['status', 'title', 'detail']]])
            ->assertJsonPath('errors.0.status', '401');
    }

    public function testGetMeReturnsUserResource(): void
    {
        $response = $this->api()->getJson('/api/v1/me');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'jsonapi',
                'data' => ['type', 'id', 'attributes', 'links'],
            ])
            ->assertJsonPath('data.type', 'users')
            ->assertJsonPath('data.id', $this->user->getUsername());
    }

    public function testPatchMeUpdatesProfile(): void
    {
        $response = $this->api()->patchJson('/api/v1/me', [
            'data' => [
                'type' => 'users',
                'attributes' => ['name' => 'Test Updated Name'],
            ],
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.attributes.name', 'Test Updated Name');
    }

    public function testRecipesListIsPaginated(): void
    {
        $response = $this->api()->getJson('/api/v1/recipes?page[size]=2');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'jsonapi',
                'data',
                'meta' => ['page'],
                'links',
            ])
            ->assertJsonPath('meta.page.per-page', 2);
    }

    public function test404ReturnsJsonapiError(): void
    {
        $response = $this->api()->getJson('/api/v1/recipes/nonexistent-slug-xyz');

        $response->assertStatus(404)
            ->assertJsonStructure(['jsonapi', 'errors' => [['status', 'title', 'detail']]])
            ->assertJsonPath('errors.0.status', '404');
    }

    public function testPlansListReturnsCatalog(): void
    {
        $response = $this->api()->getJson('/api/v1/plans');

        $response->assertStatus(200)
            ->assertJsonStructure(['jsonapi', 'data']);
    }

    public function testMeSubscriptionReturnsFreeWhenNone(): void
    {
        $response = $this->api()->getJson('/api/v1/me/subscription');

        $response->assertStatus(200)
            ->assertJsonPath('meta.plan', 'free');
    }

    public function testPreferencesReturnsDefaults(): void
    {
        $response = $this->api()->getJson('/api/v1/me/preferences');

        $response->assertStatus(200)
            ->assertJsonStructure(['jsonapi', 'data' => ['type', 'id', 'attributes']])
            ->assertJsonPath('data.type', 'user-preferences');
    }

    public function testCollectionsCrudLifecycle(): void
    {
        $create = $this->api()->postJson('/api/v1/collections', [
            'data' => [
                'type' => 'collections',
                'attributes' => ['name' => 'Test Bag', 'type' => 'bag'],
            ],
        ]);

        $create->assertStatus(201)
            ->assertJsonPath('data.type', 'collections')
            ->assertJsonPath('data.attributes.name', 'Test Bag');

        $id = $create->json('data.id');

        $show = $this->api()->getJson("/api/v1/collections/{$id}");
        $show->assertStatus(200)->assertJsonPath('data.id', $id);

        $update = $this->api()->patchJson("/api/v1/collections/{$id}", [
            'data' => [
                'type' => 'collections',
                'attributes' => ['name' => 'Renamed Bag'],
            ],
        ]);
        $update->assertStatus(200)->assertJsonPath('data.attributes.name', 'Renamed Bag');

        $delete = $this->api()->deleteJson("/api/v1/collections/{$id}");
        $delete->assertStatus(200)->assertJsonPath('meta.message', 'Collection deleted.');
    }

    public function testFollowsCreateAndList(): void
    {
        $create = $this->api()->postJson('/api/v1/follows', [
            'data' => [
                'type' => 'follows',
                'attributes' => ['followable-type' => 'authors', 'followable-id' => 99999],
            ],
        ]);

        $create->assertStatus(201)
            ->assertJsonPath('data.type', 'follows');

        $list = $this->api()->getJson('/api/v1/me/following');
        $list->assertStatus(200)->assertJsonStructure(['jsonapi', 'data', 'meta', 'links']);
    }

    public function testRegisterCreatesUser(): void
    {
        $username = 'test-register-' . time();
        $response = $this->withHeader('Content-Type', 'application/vnd.api+json')
            ->postJson('/api/register', [
                'data' => [
                    'type' => 'users',
                    'attributes' => [
                        'username' => $username,
                        'name' => 'Reg Test',
                        'email' => "{$username}@example.com",
                        'password' => 'secret1234',
                    ],
                ],
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.type', 'users')
            ->assertJsonPath('data.id', $username);
    }
}

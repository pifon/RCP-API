<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Entities\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Helpers\CreatesTestUser;
use Tests\Helpers\JsonApiRequests;
use Tests\TestCase;

class PantryTest extends TestCase
{
    use CreatesTestUser;
    use DatabaseTransactions;
    use JsonApiRequests;

    private User $user;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createOrGetTestUser();
        $this->token = auth('api')->login($this->user);
    }

    protected function getAuthToken(): string
    {
        return $this->token;
    }

    private function addPantryItem(int $productId = 1, float $quantity = 500): string
    {
        $response = $this->apiPost('/api/v1/pantry', [
            'data' => [
                'type' => 'pantry-items',
                'attributes' => ['quantity' => $quantity],
                'relationships' => [
                    'product' => ['data' => ['id' => $productId]],
                ],
            ],
        ]);
        $response->assertStatus(201);

        return $response->json('data.id');
    }

    public function test_create_pantry_item(): void
    {
        $response = $this->apiPost('/api/v1/pantry', [
            'data' => [
                'type' => 'pantry-items',
                'attributes' => ['quantity' => 1000],
                'relationships' => [
                    'product' => ['data' => ['id' => 1]],
                ],
            ],
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.type', 'pantry-items');
    }

    public function test_list_pantry_items(): void
    {
        $this->addPantryItem();

        $response = $this->apiGet('/api/v1/pantry');

        $response->assertOk()
            ->assertJsonStructure(['jsonapi', 'data']);
    }

    public function test_show_pantry_item(): void
    {
        $id = $this->addPantryItem();

        $response = $this->apiGet("/api/v1/pantry/{$id}");

        $response->assertOk()
            ->assertJsonPath('data.type', 'pantry-items')
            ->assertJsonPath('data.id', $id);
    }

    public function test_update_pantry_item(): void
    {
        $id = $this->addPantryItem(1, 500);

        $response = $this->apiPatch("/api/v1/pantry/{$id}", [
            'data' => [
                'type' => 'pantry-items',
                'attributes' => ['quantity' => 750],
            ],
        ]);

        $response->assertOk();
    }

    public function test_delete_pantry_item(): void
    {
        $id = $this->addPantryItem();

        $response = $this->apiDelete("/api/v1/pantry/{$id}");

        $response->assertOk();
    }

    public function test_consume_pantry_item(): void
    {
        $id = $this->addPantryItem(1, 1000);

        $response = $this->apiPost("/api/v1/pantry/{$id}/consume", [
            'data' => [
                'type' => 'pantry-items',
                'attributes' => ['quantity' => 200],
            ],
        ]);

        $response->assertOk();
    }

    public function test_cookable_recipes(): void
    {
        $response = $this->apiGet('/api/v1/pantry/cookable');

        $response->assertOk()
            ->assertJsonStructure(['jsonapi', 'data']);
    }
}

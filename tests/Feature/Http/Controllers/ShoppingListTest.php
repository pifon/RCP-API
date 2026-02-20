<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Entities\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Helpers\CreatesTestUser;
use Tests\Helpers\JsonApiRequests;
use Tests\TestCase;

class ShoppingListTest extends TestCase
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

    private function createList(string $name = 'Test List'): array
    {
        $response = $this->apiPost('/api/v1/shopping-lists', [
            'data' => [
                'type' => 'shopping-lists',
                'attributes' => ['name' => $name],
            ],
        ]);
        $response->assertStatus(201);

        return ['id' => $response->json('data.id'), 'slug' => $response->json('data.id')];
    }

    public function testCreateShoppingList(): void
    {
        $response = $this->apiPost('/api/v1/shopping-lists', [
            'data' => [
                'type' => 'shopping-lists',
                'attributes' => ['name' => 'Groceries ' . time()],
            ],
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.type', 'shopping-lists');
    }

    public function testListShoppingLists(): void
    {
        $this->createList('List A');

        $response = $this->apiGet('/api/v1/shopping-lists');

        $response->assertOk()
            ->assertJsonStructure(['jsonapi', 'data']);
    }

    public function testShowShoppingList(): void
    {
        $list = $this->createList('Show Test');
        $id = $list['id'];

        $response = $this->apiGet("/api/v1/shopping-lists/{$id}");

        $response->assertOk()
            ->assertJsonPath('data.type', 'shopping-lists')
            ->assertJsonPath('data.id', $id);
    }

    public function testUpdateShoppingList(): void
    {
        $list = $this->createList('Before Update');
        $id = $list['id'];

        $response = $this->apiPatch("/api/v1/shopping-lists/{$id}", [
            'data' => [
                'type' => 'shopping-lists',
                'attributes' => ['name' => 'After Update'],
            ],
        ]);

        $response->assertOk()
            ->assertJsonPath('data.attributes.name', 'After Update');
    }

    public function testDeleteShoppingList(): void
    {
        $list = $this->createList('To Delete');
        $id = $list['id'];

        $response = $this->apiDelete("/api/v1/shopping-lists/{$id}");

        $response->assertOk();
    }

    public function testAddItemToShoppingList(): void
    {
        $list = $this->createList('Items Test');
        $listId = $list['id'];

        $response = $this->apiPost("/api/v1/shopping-lists/{$listId}/items", [
            'data' => [
                'type' => 'shopping-list-items',
                'attributes' => ['quantity' => 2],
                'relationships' => [
                    'product' => ['data' => ['id' => 1]],
                ],
            ],
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.type', 'shopping-list-items');
    }

    public function testListItemsInShoppingList(): void
    {
        $list = $this->createList('Items List');
        $listId = $list['id'];

        $this->apiPost("/api/v1/shopping-lists/{$listId}/items", [
            'data' => [
                'type' => 'shopping-list-items',
                'attributes' => ['quantity' => 1],
                'relationships' => [
                    'product' => ['data' => ['id' => 1]],
                ],
            ],
        ])->assertStatus(201);

        $response = $this->apiGet("/api/v1/shopping-lists/{$listId}/items");

        $response->assertOk()
            ->assertJsonStructure(['jsonapi', 'data']);
    }
}

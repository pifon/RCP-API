<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Recipe;

use App\Entities\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Helpers\CreatesTestUser;
use Tests\Helpers\JsonApiRequests;
use Tests\TestCase;

class StepByStepTest extends TestCase
{
    use CreatesTestUser;
    use DatabaseTransactions;
    use JsonApiRequests;
    use \Tests\Helpers\CreatesTestRecipe;

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

    private function createEmptyRecipe(): string
    {
        $response = $this->apiPost('/api/v1/recipes', [
            'data' => [
                'type' => 'recipes',
                'attributes' => ['title' => 'Step Test '.time().'-'.random_int(1000, 9999)],
                'relationships' => $this->cuisineRelationship(),
            ],
        ]);
        $response->assertStatus(201);

        return $response->json('data.id');
    }

    // ── Ingredients ──────────────────────────────────────────────

    public function test_add_ingredient_to_recipe(): void
    {
        $slug = $this->createEmptyRecipe();

        $response = $this->apiPost("/api/v1/recipes/{$slug}/ingredients", [
            'data' => [
                'type' => 'ingredients',
                'attributes' => ['amount' => 500],
                'relationships' => [
                    'product' => ['data' => ['id' => 1]],
                    'measure' => ['data' => ['id' => 1]],
                ],
            ],
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.type', 'ingredients');
    }

    public function test_list_ingredients_for_recipe(): void
    {
        $slug = $this->createEmptyRecipe();

        $this->apiPost("/api/v1/recipes/{$slug}/ingredients", [
            'data' => [
                'type' => 'ingredients',
                'attributes' => ['amount' => 100],
                'relationships' => [
                    'product' => ['data' => ['id' => 1]],
                    'measure' => ['data' => ['id' => 1]],
                ],
            ],
        ])->assertStatus(201);

        $response = $this->apiGet("/api/v1/recipes/{$slug}/ingredients");

        $response->assertOk()
            ->assertJsonStructure(['jsonapi', 'data'])
            ->assertJsonCount(1, 'data');
    }

    public function test_add_ingredient_requires_amount(): void
    {
        $slug = $this->createEmptyRecipe();

        $response = $this->apiPost("/api/v1/recipes/{$slug}/ingredients", [
            'data' => [
                'type' => 'ingredients',
                'attributes' => [],
                'relationships' => [
                    'product' => ['data' => ['id' => 1]],
                    'measure' => ['data' => ['id' => 1]],
                ],
            ],
        ]);

        $response->assertStatus(422);
    }

    public function test_add_ingredient_requires_product_and_measure(): void
    {
        $slug = $this->createEmptyRecipe();

        $response = $this->apiPost("/api/v1/recipes/{$slug}/ingredients", [
            'data' => [
                'type' => 'ingredients',
                'attributes' => ['amount' => 100],
            ],
        ]);

        $response->assertStatus(422);
    }

    public function test_remove_ingredient_from_recipe(): void
    {
        $slug = $this->createEmptyRecipe();

        $add = $this->apiPost("/api/v1/recipes/{$slug}/ingredients", [
            'data' => [
                'type' => 'ingredients',
                'attributes' => ['amount' => 200],
                'relationships' => [
                    'product' => ['data' => ['id' => 1]],
                    'measure' => ['data' => ['id' => 1]],
                ],
            ],
        ]);
        $add->assertStatus(201);
        $ingredientId = $add->json('data.id');

        $delete = $this->apiDelete("/api/v1/recipes/{$slug}/ingredients/{$ingredientId}");
        $delete->assertOk();

        $list = $this->apiGet("/api/v1/recipes/{$slug}/ingredients");
        $list->assertOk()->assertJsonCount(0, 'data');
    }

    // ── Directions ───────────────────────────────────────────────

    public function test_add_direction_to_recipe(): void
    {
        $slug = $this->createEmptyRecipe();

        $response = $this->apiPost("/api/v1/recipes/{$slug}/directions", [
            'data' => [
                'type' => 'directions',
                'attributes' => [
                    'action' => 'stir',
                    'duration-minutes' => 5,
                ],
            ],
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.type', 'directions');
    }

    public function test_add_direction_auto_creates_ingredient(): void
    {
        $slug = $this->createEmptyRecipe();

        $response = $this->apiPost("/api/v1/recipes/{$slug}/directions", [
            'data' => [
                'type' => 'directions',
                'attributes' => [
                    'action' => 'sieve',
                    'duration-minutes' => 2,
                    'product-id' => 1,
                    'amount' => 500,
                    'measure-id' => 1,
                ],
            ],
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['meta' => ['ingredient-linked']]);

        $this->assertNotNull($response->json('meta.ingredient-linked.id'));

        $ingredients = $this->apiGet("/api/v1/recipes/{$slug}/ingredients");
        $ingredients->assertOk()->assertJsonCount(1, 'data');
    }

    public function test_add_direction_accumulates_ingredient_amount(): void
    {
        $slug = $this->createEmptyRecipe();

        $this->apiPost("/api/v1/recipes/{$slug}/directions", [
            'data' => [
                'type' => 'directions',
                'attributes' => [
                    'action' => 'sieve',
                    'duration-minutes' => 2,
                    'product-id' => 1,
                    'amount' => 300,
                    'measure-id' => 1,
                ],
            ],
        ])->assertStatus(201);

        $second = $this->apiPost("/api/v1/recipes/{$slug}/directions", [
            'data' => [
                'type' => 'directions',
                'attributes' => [
                    'action' => 'add more',
                    'duration-minutes' => 1,
                    'product-id' => 1,
                    'amount' => 200,
                    'measure-id' => 1,
                ],
            ],
        ]);
        $second->assertStatus(201);

        $this->assertEquals(500.0, $second->json('meta.ingredient-linked.amount'));

        $ingredients = $this->apiGet("/api/v1/recipes/{$slug}/ingredients");
        $ingredients->assertOk()->assertJsonCount(1, 'data');
    }

    public function test_add_direction_recalculates_prep_time(): void
    {
        $slug = $this->createEmptyRecipe();

        $this->apiPost("/api/v1/recipes/{$slug}/directions", [
            'data' => [
                'type' => 'directions',
                'attributes' => ['action' => 'mix', 'duration-minutes' => 5],
            ],
        ])->assertStatus(201);

        $second = $this->apiPost("/api/v1/recipes/{$slug}/directions", [
            'data' => [
                'type' => 'directions',
                'attributes' => ['action' => 'knead', 'duration-minutes' => 10],
            ],
        ]);

        $second->assertStatus(201)
            ->assertJsonPath('meta.prep-time-minutes', 15);
    }

    public function test_list_directions_for_recipe(): void
    {
        $slug = $this->createEmptyRecipe();

        $this->apiPost("/api/v1/recipes/{$slug}/directions", [
            'data' => ['type' => 'directions', 'attributes' => ['action' => 'step one', 'duration-minutes' => 3]],
        ])->assertStatus(201);

        $this->apiPost("/api/v1/recipes/{$slug}/directions", [
            'data' => ['type' => 'directions', 'attributes' => ['action' => 'step two', 'duration-minutes' => 7]],
        ])->assertStatus(201);

        $response = $this->apiGet("/api/v1/recipes/{$slug}/directions");

        $response->assertOk()
            ->assertJsonStructure(['jsonapi', 'data'])
            ->assertJsonCount(2, 'data');
    }

    public function test_add_direction_requires_action(): void
    {
        $slug = $this->createEmptyRecipe();

        $response = $this->apiPost("/api/v1/recipes/{$slug}/directions", [
            'data' => [
                'type' => 'directions',
                'attributes' => ['duration-minutes' => 5],
            ],
        ]);

        $response->assertStatus(422);
    }

    public function test_insert_direction_at_specific_step(): void
    {
        $slug = $this->createEmptyRecipe();

        $this->apiPost("/api/v1/recipes/{$slug}/directions", [
            'data' => ['type' => 'directions', 'attributes' => ['action' => 'first', 'duration-minutes' => 1]],
        ])->assertStatus(201);

        $this->apiPost("/api/v1/recipes/{$slug}/directions", [
            'data' => ['type' => 'directions', 'attributes' => ['action' => 'third', 'duration-minutes' => 1]],
        ])->assertStatus(201);

        $injectedAttrs = ['action' => 'injected', 'step' => 2, 'duration-minutes' => 1];
        $this->apiPost("/api/v1/recipes/{$slug}/directions", [
            'data' => ['type' => 'directions', 'attributes' => $injectedAttrs],
        ])->assertStatus(201);

        $list = $this->apiGet("/api/v1/recipes/{$slug}/directions");
        $list->assertOk()->assertJsonCount(3, 'data');

        $actions = array_map(
            fn ($d) => $d['attributes']['action'] ?? $d['attributes']['operation'] ?? null,
            $list->json('data'),
        );

        $this->assertEquals('first', $actions[0]);
        $this->assertEquals('injected', $actions[1]);
        $this->assertEquals('third', $actions[2]);
    }

    public function test_remove_direction_from_recipe(): void
    {
        $slug = $this->createEmptyRecipe();

        $add = $this->apiPost("/api/v1/recipes/{$slug}/directions", [
            'data' => ['type' => 'directions', 'attributes' => ['action' => 'remove me', 'duration-minutes' => 5]],
        ]);
        $add->assertStatus(201);
        $directionId = $add->json('data.id');

        $delete = $this->apiDelete("/api/v1/recipes/{$slug}/directions/{$directionId}");
        $delete->assertOk();

        $list = $this->apiGet("/api/v1/recipes/{$slug}/directions");
        $list->assertOk()->assertJsonCount(0, 'data');
    }
}

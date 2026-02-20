<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Recipe;

use App\Entities\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Helpers\CreatesTestUser;
use Tests\Helpers\JsonApiRequests;
use Tests\TestCase;

class CrudTest extends TestCase
{
    use CreatesTestUser;
    use \Tests\Helpers\CreatesTestRecipe;
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

    // ── Index ────────────────────────────────────────────────────

    public function testIndexReturnsJsonApiCollection(): void
    {
        $response = $this->apiGet('/api/v1/recipes');

        $response->assertOk()
            ->assertJsonStructure([
                'jsonapi',
                'data',
                'meta' => ['page' => ['current-page', 'per-page', 'total', 'last-page']],
                'links' => ['first', 'last'],
            ])
            ->assertJsonPath('jsonapi.version', '1.1');
    }

    public function testIndexRespectsPageSize(): void
    {
        $response = $this->apiGet('/api/v1/recipes?page[size]=3');

        $response->assertOk()
            ->assertJsonPath('meta.page.per-page', 3);

        $this->assertLessThanOrEqual(3, count($response->json('data')));
    }

    public function testIndexDataItemsHaveRecipeStructure(): void
    {
        $response = $this->apiGet('/api/v1/recipes?page[size]=1');

        $response->assertOk();

        $data = $response->json('data');
        if (count($data) > 0) {
            $item = $data[0];
            $this->assertEquals('recipes', $item['type']);
            $this->assertArrayHasKey('id', $item);
            $this->assertArrayHasKey('attributes', $item);
            $this->assertArrayHasKey('title', $item['attributes']);
        }
    }

    public function testIndexSortByTitle(): void
    {
        $response = $this->apiGet('/api/v1/recipes?sort=title&page[size]=5');

        $response->assertOk();

        $titles = array_column($response->json('data'), 'attributes');
        $titles = array_column($titles, 'title');
        $sorted = $titles;
        sort($sorted, SORT_STRING | SORT_FLAG_CASE);

        $this->assertEquals($sorted, $titles);
    }

    // ── Show ─────────────────────────────────────────────────────

    public function testShowReturnsRecipeBySlug(): void
    {
        $response = $this->apiGet('/api/v1/recipes/pizza');

        $response->assertOk()
            ->assertJsonStructure([
                'jsonapi',
                'data' => ['type', 'id', 'attributes', 'links'],
            ])
            ->assertJsonPath('data.type', 'recipes')
            ->assertJsonPath('data.id', 'pizza');
    }

    public function testShowReturns404ForMissingRecipe(): void
    {
        $response = $this->apiGet('/api/v1/recipes/does-not-exist-xyz');

        $response->assertNotFound()
            ->assertJsonPath('errors.0.status', '404');
    }

    // ── Create ───────────────────────────────────────────────────

    public function testCreateRecipeMinimalFields(): void
    {
        $response = $this->apiPost('/api/v1/recipes', [
            'data' => [
                'type' => 'recipes',
                'attributes' => [
                    'title' => 'CI Test Recipe ' . time(),
                ],
                'relationships' => $this->cuisineRelationship(),
            ],
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.type', 'recipes')
            ->assertJsonStructure([
                'data' => ['type', 'id', 'attributes' => ['title']],
            ]);
    }

    public function testCreateRecipeAllOptionalFields(): void
    {
        $title = 'Full Recipe ' . time();

        $response = $this->apiPost('/api/v1/recipes', [
            'data' => [
                'type' => 'recipes',
                'attributes' => [
                    'title' => $title,
                    'description' => 'A test description',
                    'prep-time-minutes' => 15,
                    'cook-time-minutes' => 30,
                    'difficulty' => 'medium',
                    'serves' => 4,
                ],
                'relationships' => $this->cuisineRelationship(),
            ],
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.attributes.title', $title)
            ->assertJsonPath('data.attributes.description', 'A test description')
            ->assertJsonPath('data.attributes.difficulty', 'medium');
    }

    public function testCreateRecipeRequiresTitle(): void
    {
        $response = $this->apiPost('/api/v1/recipes', [
            'data' => [
                'type' => 'recipes',
                'attributes' => [
                    'description' => 'Missing title',
                ],
            ],
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('errors.0.status', '422');
    }

    public function testCreateRecipeGeneratesUniqueSlug(): void
    {
        $title = 'Duplicate Slug Test ' . time();
        $rels = $this->cuisineRelationship();

        $first = $this->apiPost('/api/v1/recipes', [
            'data' => ['type' => 'recipes', 'attributes' => ['title' => $title], 'relationships' => $rels],
        ]);
        $first->assertStatus(201);

        $second = $this->apiPost('/api/v1/recipes', [
            'data' => ['type' => 'recipes', 'attributes' => ['title' => $title], 'relationships' => $rels],
        ]);
        $second->assertStatus(201);

        $this->assertNotEquals(
            $first->json('data.id'),
            $second->json('data.id'),
        );
    }

    // ── Create Full ──────────────────────────────────────────────

    public function testCreateFullRecipeWithIngredientsAndDirections(): void
    {
        $title = 'Full Pancakes ' . time();

        $response = $this->apiPost('/api/v1/recipes/full', [
            'data' => [
                'type' => 'recipes',
                'attributes' => [
                    'title' => $title,
                    'description' => 'Fluffy pancakes',
                    'prep-time-minutes' => 10,
                    'cook-time-minutes' => 20,
                    'difficulty' => 'easy',
                    'serves' => 4,
                ],
                'relationships' => $this->cuisineRelationship(),
            ],
            'ingredients' => [
                ['product-id' => 1, 'measure-id' => 1, 'amount' => 500],
            ],
            'directions' => [
                ['action' => 'mix', 'duration-minutes' => 5, 'ingredient' => 1],
            ],
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.type', 'recipes')
            ->assertJsonPath('data.attributes.title', $title)
            ->assertJsonPath('meta.ingredients-created', 1)
            ->assertJsonPath('meta.directions-created', 1);
    }

    public function testCreateFullRecipeValidationRequiresTitle(): void
    {
        $response = $this->apiPost('/api/v1/recipes/full', [
            'data' => [
                'type' => 'recipes',
                'attributes' => [],
            ],
        ]);

        $response->assertStatus(422);
    }
}

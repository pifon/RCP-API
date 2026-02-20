<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Recipe;

use App\Entities\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Helpers\CreatesTestUser;
use Tests\Helpers\JsonApiRequests;
use Tests\TestCase;

class UpdateDeleteTest extends TestCase
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

    private function createDraftRecipe(string $suffix = ''): string
    {
        $title = 'Update Test Recipe '.time().$suffix;
        $response = $this->apiPost('/api/v1/recipes', [
            'data' => [
                'type' => 'recipes',
                'attributes' => ['title' => $title],
                'relationships' => $this->cuisineRelationship(),
            ],
        ]);
        $response->assertStatus(201);

        return $response->json('data.id');
    }

    // ── Update ─────────────────────────────────────────────────────

    public function test_update_title(): void
    {
        $slug = $this->createDraftRecipe('-title');

        $response = $this->apiPatch("/api/v1/recipes/{$slug}", [
            'data' => [
                'type' => 'recipes',
                'id' => $slug,
                'attributes' => ['title' => 'Updated Title'],
            ],
        ]);

        $response->assertOk()
            ->assertJsonPath('data.type', 'recipes')
            ->assertJsonPath('data.attributes.title', 'Updated Title');
    }

    public function test_update_description(): void
    {
        $slug = $this->createDraftRecipe('-desc');

        $response = $this->apiPatch("/api/v1/recipes/{$slug}", [
            'data' => [
                'type' => 'recipes',
                'id' => $slug,
                'attributes' => ['description' => 'New description'],
            ],
        ]);

        $response->assertOk()
            ->assertJsonPath('data.attributes.description', 'New description');
    }

    public function test_update_multiple_fields(): void
    {
        $slug = $this->createDraftRecipe('-multi');

        $response = $this->apiPatch("/api/v1/recipes/{$slug}", [
            'data' => [
                'type' => 'recipes',
                'id' => $slug,
                'attributes' => [
                    'difficulty' => 'hard',
                    'prep-time-minutes' => 20,
                    'cook-time-minutes' => 45,
                    'serves' => 6,
                ],
            ],
        ]);

        $response->assertOk()
            ->assertJsonPath('data.attributes.difficulty', 'hard')
            ->assertJsonPath('data.attributes.prep-time-minutes', 20)
            ->assertJsonPath('data.attributes.cook-time-minutes', 45)
            ->assertJsonPath('data.attributes.serves', 6)
            ->assertJsonPath('data.attributes.total-time-minutes', 65);
    }

    public function test_publish_sets_published_at(): void
    {
        $slug = $this->createDraftRecipe('-pub');

        $response = $this->apiPatch("/api/v1/recipes/{$slug}", [
            'data' => [
                'type' => 'recipes',
                'id' => $slug,
                'attributes' => ['status' => 'published'],
            ],
        ]);

        $response->assertOk()
            ->assertJsonPath('data.attributes.status', 'published');

        $this->assertNotNull($response->json('data.attributes.published-at'));
    }

    public function test_update_idempotent(): void
    {
        $slug = $this->createDraftRecipe('-idem');

        $payload = [
            'data' => [
                'type' => 'recipes',
                'id' => $slug,
                'attributes' => ['difficulty' => 'easy', 'serves' => 2],
            ],
        ];

        $first = $this->apiPatch("/api/v1/recipes/{$slug}", $payload);
        $second = $this->apiPatch("/api/v1/recipes/{$slug}", $payload);

        $first->assertOk();
        $second->assertOk();

        $this->assertEquals(
            $first->json('data.attributes.difficulty'),
            $second->json('data.attributes.difficulty'),
        );
        $this->assertEquals(
            $first->json('data.attributes.serves'),
            $second->json('data.attributes.serves'),
        );
    }

    public function test_update_rejects_invalid_difficulty(): void
    {
        $slug = $this->createDraftRecipe('-inv');

        $response = $this->apiPatch("/api/v1/recipes/{$slug}", [
            'data' => [
                'type' => 'recipes',
                'id' => $slug,
                'attributes' => ['difficulty' => 'impossible'],
            ],
        ]);

        $response->assertStatus(422);
    }

    public function test_update_rejects_invalid_status(): void
    {
        $slug = $this->createDraftRecipe('-stat');

        $response = $this->apiPatch("/api/v1/recipes/{$slug}", [
            'data' => [
                'type' => 'recipes',
                'id' => $slug,
                'attributes' => ['status' => 'archived'],
            ],
        ]);

        $response->assertStatus(422);
    }

    public function test_update_rejects_negative_prep_time(): void
    {
        $slug = $this->createDraftRecipe('-neg');

        $response = $this->apiPatch("/api/v1/recipes/{$slug}", [
            'data' => [
                'type' => 'recipes',
                'id' => $slug,
                'attributes' => ['prep-time-minutes' => -5],
            ],
        ]);

        $response->assertStatus(422);
    }

    public function test_update_non_existent_recipe(): void
    {
        $response = $this->apiPatch('/api/v1/recipes/does-not-exist-xyz', [
            'data' => [
                'type' => 'recipes',
                'id' => 'does-not-exist-xyz',
                'attributes' => ['title' => 'Nope'],
            ],
        ]);

        $response->assertNotFound();
    }

    public function test_update_clear_nullable_field(): void
    {
        $slug = $this->createDraftRecipe('-null');

        $this->apiPatch("/api/v1/recipes/{$slug}", [
            'data' => [
                'type' => 'recipes',
                'id' => $slug,
                'attributes' => ['description' => 'Temporary'],
            ],
        ])->assertOk();

        $response = $this->apiPatch("/api/v1/recipes/{$slug}", [
            'data' => [
                'type' => 'recipes',
                'id' => $slug,
                'attributes' => ['description' => null],
            ],
        ]);

        $response->assertOk()
            ->assertJsonPath('data.attributes.description', null);
    }

    public function test_update_preserves_unchanged_fields(): void
    {
        $slug = $this->createDraftRecipe('-preserve');

        $this->apiPatch("/api/v1/recipes/{$slug}", [
            'data' => [
                'type' => 'recipes',
                'id' => $slug,
                'attributes' => [
                    'difficulty' => 'medium',
                    'serves' => 4,
                ],
            ],
        ])->assertOk();

        $response = $this->apiPatch("/api/v1/recipes/{$slug}", [
            'data' => [
                'type' => 'recipes',
                'id' => $slug,
                'attributes' => ['serves' => 8],
            ],
        ]);

        $response->assertOk()
            ->assertJsonPath('data.attributes.difficulty', 'medium')
            ->assertJsonPath('data.attributes.serves', 8);
    }

    // ── Delete ─────────────────────────────────────────────────────

    public function test_delete_recipe(): void
    {
        $slug = $this->createDraftRecipe('-del');

        $response = $this->apiDelete("/api/v1/recipes/{$slug}");

        $response->assertOk()
            ->assertJsonPath('meta.message', 'Recipe deleted.');

        $this->apiGet("/api/v1/recipes/{$slug}")->assertNotFound();
    }

    public function test_delete_non_existent_recipe(): void
    {
        $response = $this->apiDelete('/api/v1/recipes/does-not-exist-xyz');

        $response->assertNotFound();
    }

    public function test_delete_is_idempotent(): void
    {
        $slug = $this->createDraftRecipe('-del2');

        $this->apiDelete("/api/v1/recipes/{$slug}")->assertOk();
        $this->apiDelete("/api/v1/recipes/{$slug}")->assertNotFound();
    }

    // ── Step-by-step workflow ──────────────────────────────────────

    public function test_step_by_step_workflow(): void
    {
        $slug = $this->createDraftRecipe('-flow');

        $show = $this->apiGet("/api/v1/recipes/{$slug}");
        $show->assertOk()
            ->assertJsonPath('data.attributes.status', 'draft');

        $this->apiPatch("/api/v1/recipes/{$slug}", [
            'data' => [
                'type' => 'recipes',
                'id' => $slug,
                'attributes' => [
                    'description' => 'A lovely dish',
                    'difficulty' => 'easy',
                    'serves' => 2,
                    'prep-time-minutes' => 10,
                    'cook-time-minutes' => 20,
                ],
            ],
        ])->assertOk();

        $publish = $this->apiPatch("/api/v1/recipes/{$slug}", [
            'data' => [
                'type' => 'recipes',
                'id' => $slug,
                'attributes' => ['status' => 'published'],
            ],
        ]);

        $publish->assertOk()
            ->assertJsonPath('data.attributes.status', 'published')
            ->assertJsonPath('data.attributes.description', 'A lovely dish')
            ->assertJsonPath('data.attributes.difficulty', 'easy')
            ->assertJsonPath('data.attributes.serves', 2)
            ->assertJsonPath('data.attributes.total-time-minutes', 30);

        $this->assertNotNull($publish->json('data.attributes.published-at'));
    }
}

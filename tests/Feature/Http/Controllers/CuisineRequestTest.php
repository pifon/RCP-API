<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Entities\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Helpers\CreatesTestRecipe;
use Tests\Helpers\CreatesTestUser;
use Tests\Helpers\JsonApiRequests;
use Tests\TestCase;

class CuisineRequestTest extends TestCase
{
    use CreatesTestRecipe;
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

    // ── Create Request ─────────────────────────────────────────────

    public function testCreateCuisineRequest(): void
    {
        $response = $this->apiPost('/api/v1/cuisine-requests', [
            'data' => [
                'type' => 'cuisine-requests',
                'attributes' => [
                    'name' => 'Ethiopian',
                    'description' => 'East African cuisine',
                ],
            ],
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.type', 'cuisine-requests')
            ->assertJsonPath('data.attributes.name', 'Ethiopian')
            ->assertJsonPath('data.attributes.status', 'pending');
    }

    public function testCreateCuisineRequestWithVariant(): void
    {
        $response = $this->apiPost('/api/v1/cuisine-requests', [
            'data' => [
                'type' => 'cuisine-requests',
                'attributes' => [
                    'name' => 'Indian',
                    'variant' => 'Goan',
                    'description' => 'Goan cuisine from western India',
                ],
            ],
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.attributes.name', 'Indian')
            ->assertJsonPath('data.attributes.variant', 'Goan')
            ->assertJsonPath('data.attributes.full-name', 'Indian - Goan');
    }

    public function testCreateCuisineRequestRequiresName(): void
    {
        $response = $this->apiPost('/api/v1/cuisine-requests', [
            'data' => [
                'type' => 'cuisine-requests',
                'attributes' => [
                    'description' => 'Missing name',
                ],
            ],
        ]);

        $response->assertStatus(422);
    }

    // ── List & Show ────────────────────────────────────────────────

    public function testListPendingCuisineRequests(): void
    {
        $this->apiPost('/api/v1/cuisine-requests', [
            'data' => [
                'type' => 'cuisine-requests',
                'attributes' => ['name' => 'TestList ' . time()],
            ],
        ])->assertStatus(201);

        $response = $this->apiGet('/api/v1/cuisine-requests');

        $response->assertOk()
            ->assertJsonStructure([
                'jsonapi',
                'data',
                'meta' => ['page'],
                'links',
            ]);

        $this->assertGreaterThan(0, count($response->json('data')));
    }

    public function testShowCuisineRequest(): void
    {
        $create = $this->apiPost('/api/v1/cuisine-requests', [
            'data' => [
                'type' => 'cuisine-requests',
                'attributes' => ['name' => 'TestShow ' . time()],
            ],
        ]);
        $create->assertStatus(201);
        $id = $create->json('data.id');

        $response = $this->apiGet("/api/v1/cuisine-requests/{$id}");

        $response->assertOk()
            ->assertJsonPath('data.type', 'cuisine-requests')
            ->assertJsonPath('data.id', $id);
    }

    public function testShowReturns404ForMissing(): void
    {
        $response = $this->apiGet('/api/v1/cuisine-requests/999999');

        $response->assertNotFound();
    }

    // ── Approve ────────────────────────────────────────────────────

    public function testApproveCuisineRequest(): void
    {
        $create = $this->apiPost('/api/v1/cuisine-requests', [
            'data' => [
                'type' => 'cuisine-requests',
                'attributes' => [
                    'name' => 'Approve Test ' . time(),
                ],
            ],
        ]);
        $create->assertStatus(201);
        $id = $create->json('data.id');

        $response = $this->apiPost("/api/v1/cuisine-requests/{$id}/approve", [
            'data' => [
                'type' => 'cuisine-requests',
                'attributes' => ['admin-notes' => 'Looks good'],
            ],
        ]);

        $response->assertOk()
            ->assertJsonPath('data.attributes.status', 'approved')
            ->assertJsonPath('data.attributes.admin-notes', 'Looks good');

        $this->assertNotNull($response->json('data.relationships.cuisine'));
    }

    public function testApproveAlreadyApprovedRejects(): void
    {
        $create = $this->apiPost('/api/v1/cuisine-requests', [
            'data' => [
                'type' => 'cuisine-requests',
                'attributes' => ['name' => 'Double Approve ' . time()],
            ],
        ]);
        $id = $create->json('data.id');

        $this->apiPost("/api/v1/cuisine-requests/{$id}/approve")->assertOk();
        $this->apiPost("/api/v1/cuisine-requests/{$id}/approve")->assertStatus(422);
    }

    // ── Reject ─────────────────────────────────────────────────────

    public function testRejectCuisineRequest(): void
    {
        $create = $this->apiPost('/api/v1/cuisine-requests', [
            'data' => [
                'type' => 'cuisine-requests',
                'attributes' => ['name' => 'Reject Test ' . time()],
            ],
        ]);
        $id = $create->json('data.id');

        $response = $this->apiPost("/api/v1/cuisine-requests/{$id}/reject", [
            'data' => [
                'type' => 'cuisine-requests',
                'attributes' => ['admin-notes' => 'Already exists as Thai'],
            ],
        ]);

        $response->assertOk()
            ->assertJsonPath('data.attributes.status', 'rejected')
            ->assertJsonPath('data.attributes.admin-notes', 'Already exists as Thai');
    }

    public function testRejectAlreadyRejectedRejects(): void
    {
        $create = $this->apiPost('/api/v1/cuisine-requests', [
            'data' => [
                'type' => 'cuisine-requests',
                'attributes' => ['name' => 'Double Reject ' . time()],
            ],
        ]);
        $id = $create->json('data.id');

        $this->apiPost("/api/v1/cuisine-requests/{$id}/reject")->assertOk();
        $this->apiPost("/api/v1/cuisine-requests/{$id}/reject")->assertStatus(422);
    }

    // ── Recipe + Cuisine-Request integration ───────────────────────

    public function testRecipeRequiresCuisine(): void
    {
        $response = $this->apiPost('/api/v1/recipes', [
            'data' => [
                'type' => 'recipes',
                'attributes' => ['title' => 'No Cuisine ' . time()],
            ],
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('errors.0.source.pointer', '/data/relationships/cuisine');

        $links = $response->json('errors.0.links');
        $this->assertArrayHasKey('create-cuisine-request', $links);
        $this->assertEquals('/api/v1/cuisine-requests', $links['create-cuisine-request']['href']);
    }

    public function testRecipeRejectsNonExistentCuisineById(): void
    {
        $response = $this->apiPost('/api/v1/recipes', [
            'data' => [
                'type' => 'recipes',
                'attributes' => ['title' => 'Bad Cuisine ' . time()],
                'relationships' => [
                    'cuisine' => ['data' => ['type' => 'cuisines', 'id' => 999999]],
                ],
            ],
        ]);

        $response->assertStatus(422);

        $links = $response->json('errors.0.links');
        $this->assertArrayHasKey('create-cuisine-request', $links);
    }

    public function testRecipeByNameExact(): void
    {
        $response = $this->apiPost('/api/v1/recipes', [
            'data' => [
                'type' => 'recipes',
                'attributes' => ['title' => 'By Name ' . time()],
                'relationships' => [
                    'cuisine' => ['data' => ['name' => 'Italian']],
                ],
            ],
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.type', 'recipes');
    }

    public function testRecipeBySlug(): void
    {
        $response = $this->apiPost('/api/v1/recipes', [
            'data' => [
                'type' => 'recipes',
                'attributes' => ['title' => 'By Slug ' . time()],
                'relationships' => [
                    'cuisine' => ['data' => ['slug' => 'italian']],
                ],
            ],
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.type', 'recipes');
    }

    public function testMisspelledCuisineSuggestsSimilar(): void
    {
        $response = $this->apiPost('/api/v1/recipes', [
            'data' => [
                'type' => 'recipes',
                'attributes' => ['title' => 'Typo ' . time()],
                'relationships' => [
                    'cuisine' => ['data' => ['name' => 'Appulian']],
                ],
            ],
        ]);

        $response->assertStatus(422);

        $detail = $response->json('errors.0.detail');
        $this->assertStringContainsString('Did you mean', $detail);
        $this->assertStringContainsString('Apulian', $detail);

        $links = $response->json('errors.0.links');
        $this->assertArrayHasKey('create-cuisine-request', $links);

        $slugKeys = array_filter(
            array_keys($links),
            fn ($k) => str_starts_with($k, 'cuisine:'),
        );
        $this->assertNotEmpty($slugKeys);
    }

    public function testPartialNameSuggestsMatches(): void
    {
        $response = $this->apiPost('/api/v1/recipes', [
            'data' => [
                'type' => 'recipes',
                'attributes' => ['title' => 'Partial ' . time()],
                'relationships' => [
                    'cuisine' => ['data' => ['name' => 'Ital']],
                ],
            ],
        ]);

        $response->assertStatus(422);

        $detail = $response->json('errors.0.detail');
        $this->assertStringContainsString('Did you mean', $detail);
        $this->assertStringContainsString('Italian', $detail);
    }

    public function testRecipeCanReferenceCuisineRequest(): void
    {
        $cr = $this->apiPost('/api/v1/cuisine-requests', [
            'data' => [
                'type' => 'cuisine-requests',
                'attributes' => ['name' => 'CR Recipe Test ' . time()],
            ],
        ]);
        $cr->assertStatus(201);
        $crId = $cr->json('data.id');

        $response = $this->apiPost('/api/v1/recipes', [
            'data' => [
                'type' => 'recipes',
                'attributes' => ['title' => 'With CR ' . time()],
                'relationships' => [
                    'cuisine-request' => [
                        'data' => ['type' => 'cuisine-requests', 'id' => $crId],
                    ],
                ],
            ],
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.type', 'recipes');
    }

    public function testApprovalUpgradesRecipeCuisine(): void
    {
        $cr = $this->apiPost('/api/v1/cuisine-requests', [
            'data' => [
                'type' => 'cuisine-requests',
                'attributes' => ['name' => 'Upgrade Test ' . time()],
            ],
        ]);
        $crId = $cr->json('data.id');

        $recipe = $this->apiPost('/api/v1/recipes', [
            'data' => [
                'type' => 'recipes',
                'attributes' => ['title' => 'Upgrade Recipe ' . time()],
                'relationships' => [
                    'cuisine-request' => [
                        'data' => ['type' => 'cuisine-requests', 'id' => $crId],
                    ],
                ],
            ],
        ]);
        $recipe->assertStatus(201);
        $slug = $recipe->json('data.id');

        $this->apiPost("/api/v1/cuisine-requests/{$crId}/approve")->assertOk();

        $show = $this->apiGet("/api/v1/recipes/{$slug}");
        $show->assertOk();
        $this->assertNotNull($show->json('data.relationships.cuisine'));
    }
}

<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Recipe;

use App\Entities\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Helpers\CreatesTestUser;
use Tests\Helpers\JsonApiRequests;
use Tests\TestCase;

class ImportExportTest extends TestCase
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

    private function createRecipeWithIngredients(): string
    {
        $title = 'Export Test ' . time() . '-' . random_int(1000, 9999);

        $response = $this->apiPost('/api/v1/recipes/full', [
            'data' => [
                'type' => 'recipes',
                'attributes' => [
                    'title' => $title,
                    'description' => 'Recipe for export testing',
                    'prep-time-minutes' => 10,
                    'cook-time-minutes' => 20,
                    'difficulty' => 'easy',
                    'serves' => 2,
                ],
                'relationships' => $this->cuisineRelationship(),
            ],
            'ingredients' => [
                ['product-id' => 1, 'measure-id' => 1, 'amount' => 250],
            ],
            'directions' => [
                ['action' => 'sieve', 'duration-minutes' => 2, 'ingredient' => 1],
                ['action' => 'mix', 'duration-minutes' => 5],
            ],
        ]);

        $response->assertStatus(201);

        return $response->json('data.id');
    }

    // ── Export ────────────────────────────────────────────────────

    public function testExportReturnsRecipeJson(): void
    {
        $slug = $this->createRecipeWithIngredients();

        $response = $this->apiGet("/api/v1/recipes/{$slug}/export");

        $response->assertOk()
            ->assertJsonStructure([
                'pifon-recipe',
                'exported-at',
                'recipe' => ['title', 'slug', 'description', 'author'],
                'ingredients',
                'directions',
            ]);

        $this->assertEquals('1.0', $response->json('pifon-recipe'));
        $this->assertEquals($slug, $response->json('recipe.slug'));
    }

    public function testExportIncludesIngredientData(): void
    {
        $slug = $this->createRecipeWithIngredients();

        $response = $this->apiGet("/api/v1/recipes/{$slug}/export");

        $response->assertOk();

        $ingredients = $response->json('ingredients');
        $this->assertCount(1, $ingredients);
        $this->assertEquals(250, $ingredients[0]['amount']);
        $this->assertArrayHasKey('product-name', $ingredients[0]);
    }

    public function testExportIncludesDirectionData(): void
    {
        $slug = $this->createRecipeWithIngredients();

        $response = $this->apiGet("/api/v1/recipes/{$slug}/export");

        $response->assertOk();

        $directions = $response->json('directions');
        $this->assertCount(2, $directions);
        $this->assertEquals('sieve', $directions[0]['action']);
        $this->assertEquals('mix', $directions[1]['action']);
    }

    public function testExportReturns404ForMissingRecipe(): void
    {
        $response = $this->apiGet('/api/v1/recipes/nonexistent-recipe-xyz/export');

        $response->assertNotFound();
    }

    // ── Import ───────────────────────────────────────────────────

    public function testImportCreatesNewRecipe(): void
    {
        $title = 'Imported Cake ' . time();

        $response = $this->apiPost('/api/v1/recipes/import', [
            'pifon-recipe' => '1.0',
            'recipe' => [
                'title' => $title,
                'description' => 'An imported recipe',
                'difficulty' => 'medium',
                'prep-time-minutes' => 15,
                'serves' => 6,
                'cuisine' => ['id' => self::TEST_CUISINE_ID, 'name' => 'Italian'],
            ],
            'ingredients' => [
                ['product-id' => 1, 'measure-id' => 1, 'amount' => 300],
            ],
            'directions' => [
                ['action' => 'preheat', 'duration-minutes' => 10],
                ['action' => 'bake', 'duration-minutes' => 30, 'ingredient' => 1],
            ],
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.type', 'recipes')
            ->assertJsonPath('data.attributes.title', $title)
            ->assertJsonPath('meta.imported', true)
            ->assertJsonPath('meta.ingredients-created', 1)
            ->assertJsonPath('meta.directions-created', 2);
    }

    public function testImportRejectsMissingVersionHeader(): void
    {
        $response = $this->apiPost('/api/v1/recipes/import', [
            'recipe' => ['title' => 'No version'],
            'ingredients' => [],
            'directions' => [],
        ]);

        $response->assertStatus(422);
    }

    public function testImportRejectsMissingTitle(): void
    {
        $response = $this->apiPost('/api/v1/recipes/import', [
            'pifon-recipe' => '1.0',
            'recipe' => ['description' => 'No title'],
            'ingredients' => [],
            'directions' => [],
        ]);

        $response->assertStatus(422);
    }

    public function testExportThenImportRoundTrip(): void
    {
        $slug = $this->createRecipeWithIngredients();

        $exportResponse = $this->apiGet("/api/v1/recipes/{$slug}/export");
        $exportResponse->assertOk();

        $exportData = $exportResponse->json();

        $importResponse = $this->apiPost('/api/v1/recipes/import', $exportData);

        $importResponse->assertStatus(201)
            ->assertJsonPath('meta.imported', true);

        $importedSlug = $importResponse->json('data.id');
        $this->assertNotEquals($slug, $importedSlug, 'Import must create new slug');
    }

    // ── Fork ─────────────────────────────────────────────────────

    public function testForkCreatesDeepCopy(): void
    {
        $slug = $this->createRecipeWithIngredients();

        $response = $this->apiPost("/api/v1/recipes/{$slug}/fork");

        $response->assertStatus(201)
            ->assertJsonPath('data.type', 'recipes')
            ->assertJsonStructure([
                'meta' => ['forked-from', 'original-author', 'ingredients-cloned', 'directions-cloned'],
            ])
            ->assertJsonPath('meta.forked-from', $slug);

        $forkedSlug = $response->json('data.id');
        $this->assertNotEquals($slug, $forkedSlug);
        $this->assertStringContainsString('fork', $forkedSlug);
    }

    public function testForkPreservesIngredientCount(): void
    {
        $slug = $this->createRecipeWithIngredients();

        $response = $this->apiPost("/api/v1/recipes/{$slug}/fork");

        $response->assertStatus(201)
            ->assertJsonPath('meta.ingredients-cloned', 1)
            ->assertJsonPath('meta.directions-cloned', 2);
    }

    public function testForkReturns404ForMissingRecipe(): void
    {
        $response = $this->apiPost('/api/v1/recipes/nonexistent-slug-xyz/fork');

        $response->assertNotFound();
    }

    public function testForkedRecipeIsIndependent(): void
    {
        $slug = $this->createRecipeWithIngredients();

        $fork = $this->apiPost("/api/v1/recipes/{$slug}/fork");
        $fork->assertStatus(201);

        $forkedSlug = $fork->json('data.id');

        $original = $this->apiGet("/api/v1/recipes/{$slug}");
        $forked = $this->apiGet("/api/v1/recipes/{$forkedSlug}");

        $original->assertOk();
        $forked->assertOk();

        $this->assertEquals(
            $original->json('data.attributes.title'),
            $forked->json('data.attributes.title'),
        );
        $this->assertNotEquals(
            $original->json('data.id'),
            $forked->json('data.id'),
        );
    }
}

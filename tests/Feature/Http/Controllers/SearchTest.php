<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Entities\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Helpers\CreatesTestUser;
use Tests\Helpers\JsonApiRequests;
use Tests\TestCase;

class SearchTest extends TestCase
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

    // ── Recipe Search ────────────────────────────────────────────

    public function testRecipeSearchReturnsResults(): void
    {
        $response = $this->apiGet('/api/v1/recipes/search?q=pizza');

        $response->assertOk()
            ->assertJsonStructure([
                'jsonapi',
                'data',
                'meta' => ['search' => ['query', 'total-results']],
            ])
            ->assertJsonPath('meta.search.query', 'pizza');

        $this->assertGreaterThanOrEqual(1, count($response->json('data')));
    }

    public function testRecipeSearchReturnsRecipeType(): void
    {
        $response = $this->apiGet('/api/v1/recipes/search?q=pizza');

        $response->assertOk();

        $data = $response->json('data');
        if (count($data) > 0) {
            $this->assertEquals('recipes', $data[0]['type']);
            $this->assertArrayHasKey('attributes', $data[0]);
            $this->assertArrayHasKey('title', $data[0]['attributes']);
        }
    }

    public function testRecipeSearchByIngredient(): void
    {
        $response = $this->apiGet('/api/v1/recipes/search?q=mozzarella');

        $response->assertOk();
        $this->assertGreaterThanOrEqual(1, $response->json('meta.search.total-results'));
    }

    public function testRecipeSearchEmptyQueryReturnsEmpty(): void
    {
        $response = $this->apiGet('/api/v1/recipes/search?q=');

        $response->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function testRecipeSearchNoMatchReturnsEmpty(): void
    {
        $response = $this->apiGet('/api/v1/recipes/search?q=xyznonexistent99');

        $response->assertOk()
            ->assertJsonCount(0, 'data')
            ->assertJsonPath('meta.search.total-results', 0);
    }

    public function testRecipeSearchRespectsPagination(): void
    {
        $response = $this->apiGet('/api/v1/recipes/search?q=a&page[size]=3');

        $response->assertOk();
        $this->assertLessThanOrEqual(3, count($response->json('data')));
    }

    public function testRecipeSearchFilterByDifficulty(): void
    {
        $response = $this->apiGet(
            '/api/v1/recipes/search?q=a&filter[difficulty]=easy'
        );

        $response->assertOk();
        foreach ($response->json('data') as $item) {
            $this->assertEquals('easy', $item['attributes']['difficulty']);
        }
    }

    public function testRecipeSearchFilterByStatus(): void
    {
        $response = $this->apiGet(
            '/api/v1/recipes/search?q=a&filter[status]=published'
        );

        $response->assertOk();
        foreach ($response->json('data') as $item) {
            $this->assertEquals('published', $item['attributes']['status']);
        }
    }

    // ── Autocomplete ─────────────────────────────────────────────

    public function testAutocompleteReturnsSuggestions(): void
    {
        $response = $this->apiGet(
            '/api/v1/recipes/search/autocomplete?q=pi'
        );

        $response->assertOk()
            ->assertJsonStructure([
                'jsonapi',
                'data',
                'meta' => ['query', 'count'],
            ])
            ->assertJsonPath('meta.query', 'pi');

        $this->assertGreaterThanOrEqual(1, $response->json('meta.count'));
    }

    public function testAutocompleteReturnsRecipeStructure(): void
    {
        $response = $this->apiGet(
            '/api/v1/recipes/search/autocomplete?q=pa'
        );

        $response->assertOk();
        $data = $response->json('data');
        if (count($data) > 0) {
            $this->assertEquals('recipes', $data[0]['type']);
            $this->assertArrayHasKey('title', $data[0]['attributes']);
        }
    }

    public function testAutocompleteShortQueryReturnsEmpty(): void
    {
        $response = $this->apiGet(
            '/api/v1/recipes/search/autocomplete?q=p'
        );

        $response->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function testAutocompleteRespectsLimit(): void
    {
        $response = $this->apiGet(
            '/api/v1/recipes/search/autocomplete?q=pa&limit=2'
        );

        $response->assertOk();
        $this->assertLessThanOrEqual(2, count($response->json('data')));
    }

    // ── Product Search ───────────────────────────────────────────

    public function testProductSearchReturnsResults(): void
    {
        $response = $this->apiGet('/api/v1/products/search?q=salt');

        $response->assertOk()
            ->assertJsonStructure([
                'jsonapi',
                'data',
                'meta' => ['search' => ['query', 'total-results']],
            ])
            ->assertJsonPath('meta.search.query', 'salt');

        $this->assertGreaterThanOrEqual(1, count($response->json('data')));
    }

    public function testProductSearchReturnsProductType(): void
    {
        $response = $this->apiGet('/api/v1/products/search?q=salt');

        $response->assertOk();
        $data = $response->json('data');
        if (count($data) > 0) {
            $this->assertEquals('products', $data[0]['type']);
            $this->assertArrayHasKey('name', $data[0]['attributes']);
        }
    }

    public function testProductSearchEmptyQueryReturnsEmpty(): void
    {
        $response = $this->apiGet('/api/v1/products/search?q=');

        $response->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function testProductSearchNoMatchReturnsEmpty(): void
    {
        $response = $this->apiGet('/api/v1/products/search?q=xyznonexistent99');

        $response->assertOk()
            ->assertJsonCount(0, 'data')
            ->assertJsonPath('meta.search.total-results', 0);
    }

    public function testProductSearchPartialMatch(): void
    {
        $response = $this->apiGet('/api/v1/products/search?q=oil');

        $response->assertOk();
        $this->assertGreaterThanOrEqual(1, count($response->json('data')));
    }
}

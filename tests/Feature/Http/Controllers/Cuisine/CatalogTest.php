<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Cuisine;

use App\Entities\User;
use App\Repositories\v1\CuisineRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Helpers\CreatesTestUser;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class CatalogTest extends TestCase
{
    use CreatesTestUser;
    use DatabaseTransactions;

    private User $user;

    private const string API_ENDPOINT = '/api/v1/cuisines';

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createOrGetTestUser();
    }

    private function authenticatedGet(string $uri): TestResponse
    {
        $token = JWTAuth::fromUser($this->user);

        return $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/vnd.api+json',
        ])->getJson($uri);
    }

    public function test_index_returns_ok_status(): void
    {
        $response = $this->authenticatedGet(self::API_ENDPOINT);

        $response->assertOk();
    }

    public function test_index_returns_json_api_structure(): void
    {
        $response = $this->authenticatedGet(self::API_ENDPOINT);

        $response->assertOk()
            ->assertJsonStructure([
                'jsonapi' => ['version'],
                'data',
                'meta' => ['page'],
                'links',
            ])
            ->assertJsonPath('jsonapi.version', '1.1');
    }

    public function test_index_data_items_have_resource_structure(): void
    {
        $response = $this->authenticatedGet(self::API_ENDPOINT);

        $response->assertOk();

        $data = $response->json('data');
        if (count($data) > 0) {
            $first = $data[0];
            $this->assertArrayHasKey('type', $first);
            $this->assertArrayHasKey('id', $first);
            $this->assertArrayHasKey('attributes', $first);
            $this->assertEquals('cuisines', $first['type']);
        }
    }

    public function test_index_respects_page_size_parameter(): void
    {
        $response = $this->authenticatedGet(self::API_ENDPOINT.'?page[size]=2');

        $response->assertOk()
            ->assertJsonPath('meta.page.per-page', 2);

        $data = $response->json('data');
        $this->assertLessThanOrEqual(2, count($data));
    }

    public function test_index_respects_page_number_parameter(): void
    {
        $response = $this->authenticatedGet(self::API_ENDPOINT.'?page[number]=1&page[size]=5');

        $response->assertOk()
            ->assertJsonPath('meta.page.current-page', 1)
            ->assertJsonPath('meta.page.per-page', 5);
    }

    public function test_index_pagination_contains_required_links(): void
    {
        $response = $this->authenticatedGet(self::API_ENDPOINT);

        $response->assertOk()
            ->assertJsonStructure([
                'links' => ['first', 'last'],
            ]);
    }

    public function test_index_pagination_meta_has_all_fields(): void
    {
        $response = $this->authenticatedGet(self::API_ENDPOINT);

        $response->assertOk()
            ->assertJsonStructure([
                'meta' => ['page' => [
                    'current-page',
                    'per-page',
                    'from',
                    'to',
                    'total',
                    'last-page',
                ]],
            ]);
    }

    public function test_index_default_page_size_is25(): void
    {
        $response = $this->authenticatedGet(self::API_ENDPOINT);

        $response->assertOk()
            ->assertJsonPath('meta.page.per-page', 25);
    }

    public function test_index_with_mocked_empty_repository(): void
    {
        $mock = $this->createMock(CuisineRepository::class);
        $mock->method('countAll')->willReturn(0);
        $mock->method('listAll')->willReturn([]);
        $this->app->bind(CuisineRepository::class, fn () => $mock);

        $response = $this->authenticatedGet(self::API_ENDPOINT);

        $response->assertOk()
            ->assertJsonPath('data', [])
            ->assertJsonPath('meta.page.total', 0);
    }

    public function test_index_page_size_clamped_to_max100(): void
    {
        $response = $this->authenticatedGet(self::API_ENDPOINT.'?page[size]=200');

        $response->assertOk()
            ->assertJsonPath('meta.page.per-page', 100);
    }

    public function test_index_page_size_clamped_to_min1(): void
    {
        $response = $this->authenticatedGet(self::API_ENDPOINT.'?page[size]=0');

        $response->assertOk()
            ->assertJsonPath('meta.page.per-page', 1);
    }

    public function test_index_ignores_unknown_query_parameters(): void
    {
        $response = $this->authenticatedGet(self::API_ENDPOINT.'?something=unexpected');

        $response->assertOk();
    }

    #[DataProvider('disallowedHttpMethodsProvider')]
    public function test_disallowed_http_methods_return405(string $method): void
    {
        $token = JWTAuth::fromUser($this->user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/vnd.api+json',
            'Content-Type' => 'application/vnd.api+json',
        ])->json($method, self::API_ENDPOINT);

        $response->assertStatus(405);
    }

    /**
     * @return array<int, array<int, string>>
     */
    public static function disallowedHttpMethodsProvider(): array
    {
        return [
            ['POST'],
            ['PUT'],
            ['PATCH'],
            ['DELETE'],
        ];
    }

    public function test_index_sort_parameter_is_accepted(): void
    {
        $response = $this->authenticatedGet(self::API_ENDPOINT.'?sort=name');

        $response->assertOk();
    }

    public function test_index_content_type_is_json_api(): void
    {
        $response = $this->authenticatedGet(self::API_ENDPOINT);

        $response->assertOk()
            ->assertHeader('Content-Type', 'application/vnd.api+json');
    }

    public function test_index_total_matches_data_count_on_single_page(): void
    {
        $response = $this->authenticatedGet(self::API_ENDPOINT.'?page[size]=100');

        $response->assertOk();

        $total = $response->json('meta.page.total');
        $dataCount = count($response->json('data'));
        $this->assertEquals(min($total, 100), $dataCount);
    }
}

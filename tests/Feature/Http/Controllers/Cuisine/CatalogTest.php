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
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/vnd.api+json',
        ])->getJson($uri);
    }

    public function testIndexReturnsOkStatus(): void
    {
        $response = $this->authenticatedGet(self::API_ENDPOINT);

        $response->assertOk();
    }

    public function testIndexReturnsJsonApiStructure(): void
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

    public function testIndexDataItemsHaveResourceStructure(): void
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

    public function testIndexRespectsPageSizeParameter(): void
    {
        $response = $this->authenticatedGet(self::API_ENDPOINT . '?page[size]=2');

        $response->assertOk()
            ->assertJsonPath('meta.page.per-page', 2);

        $data = $response->json('data');
        $this->assertLessThanOrEqual(2, count($data));
    }

    public function testIndexRespectsPageNumberParameter(): void
    {
        $response = $this->authenticatedGet(self::API_ENDPOINT . '?page[number]=1&page[size]=5');

        $response->assertOk()
            ->assertJsonPath('meta.page.current-page', 1)
            ->assertJsonPath('meta.page.per-page', 5);
    }

    public function testIndexPaginationContainsRequiredLinks(): void
    {
        $response = $this->authenticatedGet(self::API_ENDPOINT);

        $response->assertOk()
            ->assertJsonStructure([
                'links' => ['first', 'last'],
            ]);
    }

    public function testIndexPaginationMetaHasAllFields(): void
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

    public function testIndexDefaultPageSizeIs25(): void
    {
        $response = $this->authenticatedGet(self::API_ENDPOINT);

        $response->assertOk()
            ->assertJsonPath('meta.page.per-page', 25);
    }

    public function testIndexWithMockedEmptyRepository(): void
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

    public function testIndexPageSizeClampedToMax100(): void
    {
        $response = $this->authenticatedGet(self::API_ENDPOINT . '?page[size]=200');

        $response->assertOk()
            ->assertJsonPath('meta.page.per-page', 100);
    }

    public function testIndexPageSizeClampedToMin1(): void
    {
        $response = $this->authenticatedGet(self::API_ENDPOINT . '?page[size]=0');

        $response->assertOk()
            ->assertJsonPath('meta.page.per-page', 1);
    }

    public function testIndexIgnoresUnknownQueryParameters(): void
    {
        $response = $this->authenticatedGet(self::API_ENDPOINT . '?something=unexpected');

        $response->assertOk();
    }

    #[DataProvider('disallowedHttpMethodsProvider')]
    public function testDisallowedHttpMethodsReturn405(string $method): void
    {
        $token = JWTAuth::fromUser($this->user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
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

    public function testIndexSortParameterIsAccepted(): void
    {
        $response = $this->authenticatedGet(self::API_ENDPOINT . '?sort=name');

        $response->assertOk();
    }

    public function testIndexContentTypeIsJsonApi(): void
    {
        $response = $this->authenticatedGet(self::API_ENDPOINT);

        $response->assertOk()
            ->assertHeader('Content-Type', 'application/vnd.api+json');
    }

    public function testIndexTotalMatchesDataCountOnSinglePage(): void
    {
        $response = $this->authenticatedGet(self::API_ENDPOINT . '?page[size]=100');

        $response->assertOk();

        $total = $response->json('meta.page.total');
        $dataCount = count($response->json('data'));
        $this->assertEquals(min($total, 100), $dataCount);
    }
}

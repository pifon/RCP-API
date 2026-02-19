<?php

namespace Tests\Feature\Http\Controllers\Cuisine;

use App\Entities\User;
use App\Exceptions\v1\ValidationErrorException;
use App\Repositories\v1\CuisineRepository;
use App\Transformers\v1\CuisineTransformer;
use Exception;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Helpers\CreatesTestUser;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class CatalogTest extends TestCase
{
    use CreatesTestUser;
    use DatabaseTransactions;
    use WithFaker;

    private CuisineRepository $repository;

    private CuisineTransformer $transformer;

    private User $user;

    private const string API_ENDPOINT = '/api/v1/cuisines';

    private const array ERROR_KEYS = ['status', 'message', 'errors'];

    private const array DATA_KEYS = ['data'];

    /**
     * @throws ValidationErrorException
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->createMock(CuisineRepository::class);
        $this->transformer = $this->createMock(CuisineTransformer::class);

        $this->app->bind(CuisineRepository::class, fn () => $this->repository);
        $this->app->bind(CuisineTransformer::class, fn () => $this->transformer);

        $this->user = $this->createOrGetTestUser();
    }

    protected function assertJsonHasOnlyKeys(array $expectedKeys, TestResponse $response): void
    {
        $jsonKeys = array_keys($response->json());
        sort($expectedKeys);
        sort($jsonKeys);
        $this->assertEquals($expectedKeys, $jsonKeys);
    }

    protected function assertValidationErrorResponse(TestResponse $response, string $field, string $messageKey): void
    {
        $response->assertStatus(422)
            ->assertJsonStructure(self::ERROR_KEYS)
            ->assertJsonPath("errors.$field.0", trans("cuisine.$field.$messageKey"));

        $this->assertJsonHasOnlyKeys(self::ERROR_KEYS, $response);
    }

    protected function mockRepositoryGetCuisines(?string $filter, ?int $limit, array $return): void
    {
        $this->repository->expects($this->once())
            ->method('getCuisines')
            ->with($filter, $limit)
            ->willReturn($return);
    }

    protected function mockTransformerTransformSet(array $input, array $output): void
    {
        $this->transformer->expects($this->once())
            ->method('transformSet')
            ->with($input)
            ->willReturn($output);
    }

    protected function getAuthenticated(
        string $uri,
        array $params = [],
        string $method = 'GET',
        array $headers = []
    ): TestResponse {
        $token = JWTAuth::fromUser($this->user);

        return $this->withHeaders(array_merge([
            'Authorization' => 'Bearer ' . $token,
        ], $headers))->json($method, $uri, $params);
    }

    public function testIndexWithoutParametersReturnsSuccess(): void
    {
        $this->getAuthenticated(self::API_ENDPOINT)->assertOk();
    }

    public function testIndexNoParamsReturnsDefaultLimit(): void
    {
        $this->mockRepositoryGetCuisines(null, null, ['cuisine1', 'cuisine2']);
        $this->mockTransformerTransformSet(['cuisine1', 'cuisine2'], ['data' => ['cuisine1', 'cuisine2']]);

        $response = $this->getAuthenticated(self::API_ENDPOINT);

        $response->assertOk()
            ->assertExactJson([
                'data' => ['cuisine1', 'cuisine2'],
            ]);
    }

    public function testIndexAppliesLimitParameterCorrectly(): void
    {
        $limit = 5;

        $this->mockRepositoryGetCuisines(null, $limit, ['cuisine1', 'cuisine2']);
        $this->mockTransformerTransformSet(['cuisine1', 'cuisine2'], ['data' => ['cuisine1', 'cuisine2']]);

        $response = $this->getAuthenticated(self::API_ENDPOINT . '?limit=' . $limit);

        $response->assertOk()
            ->assertExactJson([
                'data' => ['cuisine1', 'cuisine2'],
            ]);
    }

    #[DataProvider('invalidLimitProvider')]
    public function testInvalidLimitsReturnValidationError(string $limit, string $expectedMessageKey): void
    {
        $response = $this->getAuthenticated(self::API_ENDPOINT . "?limit=$limit");

        $this->assertValidationErrorResponse($response, 'limit', $expectedMessageKey);
    }

    /**
     * @return array<int, array>
     */
    public static function invalidLimitProvider(): array
    {
        return [
            ['0', 'min'],
            ['51', 'max'],
            ['abc', 'integer'],
            ['', 'integer'],
            ['null', 'integer'],
        ];
    }

    public function testIndexWithValidFilterReturnsStatusOk(): void
    {
        $this->repository
            ->expects($this->once())
            ->method('getCuisines')
            ->with('Vegan', null)
            ->willReturn([]);

        $response = $this->getAuthenticated(self::API_ENDPOINT . '?filter=vegan');

        $response->assertStatus(200);
    }

    #[DataProvider('invalidFilterProvider')]
    public function testInvalidFiltersReturnValidationError(mixed $filter, string $expectedMessageKey): void
    {
        $response = $this->getAuthenticated(self::API_ENDPOINT, ['filter' => $filter]);

        $this->assertValidationErrorResponse($response, 'filter', $expectedMessageKey);
    }

    /**
     * @return array<mixed, array>
     */
    public static function invalidFilterProvider(): array
    {
        return [
            ['VeGaN', 'lowercase'],
            ['123', 'lowercase'],
            ['', 'string'],
            [['vegan'], 'string'],
            [(object) ['value' => 'vegan'], 'string'],
            [null, 'string'],
        ];
    }

    public function testIndexWithUnexpectedFieldThrowsBadRequest(): void
    {
        $response = $this->getAuthenticated(self::API_ENDPOINT . '?something=unexpected');

        $response->assertStatus(400)
            ->assertJsonStructure(['message', 'errors']);

        $this->assertJsonHasOnlyKeys(self::ERROR_KEYS, $response);
    }

    public function testIndexWithFilterAndLimitReturnsStatusOk(): void
    {
        $this->mockRepositoryGetCuisines('Vegan', 10, ['c1', 'c2']);
        $this->mockTransformerTransformSet(['c1', 'c2'], ['data' => ['c1', 'c2']]);

        $response = $this->getAuthenticated(self::API_ENDPOINT . '?filter=vegan&limit=10');

        $response->assertOk()
            ->assertJsonStructure(['data'])
            ->assertExactJson(['data' => ['c1', 'c2']]);

        $this->assertJsonHasOnlyKeys(self::DATA_KEYS, $response);
    }

    // Similarly for your other success tests returning JSON:
    public function testIndexResponseContractWithMockedTransformer(): void
    {
        $cuisines = ['c1', 'c2'];
        $transformed = ['data' => ['c1', 'c2']];

        $this->mockRepositoryGetCuisines(null, null, $cuisines);
        $this->mockTransformerTransformSet($cuisines, $transformed);

        $response = $this->getAuthenticated(self::API_ENDPOINT);

        $response->assertStatus(200)
            ->assertExactJson($transformed);

        $this->assertJsonHasOnlyKeys(self::DATA_KEYS, $response);
    }

    public function testIndexWithEmptyFilterAndLimit(): void
    {
        $response = $this->getAuthenticated(self::API_ENDPOINT . '?filter=&limit=');
        $response->assertStatus(422);
    }

    public function testIndexLimitAtMinimumBoundary(): void
    {
        $this->mockRepositoryGetCuisines(null, 1, []);
        $this->mockTransformerTransformSet([], ['data' => []]);

        $response = $this->getAuthenticated(self::API_ENDPOINT . '?limit=1');

        $response->assertOk();
    }

    public function testIndexLimitAtMaximumBoundary(): void
    {
        $this->mockRepositoryGetCuisines(null, 50, []);
        $this->mockTransformerTransformSet([], ['data' => []]);

        $response = $this->getAuthenticated(self::API_ENDPOINT . '?limit=50');

        $response->assertOk();
    }

    public function testIndexWithFilterAsArrayRejectsWithValidationError(): void
    {
        $response = $this->getAuthenticated(self::API_ENDPOINT, ['filter' => ['vegan', 'vegetarian']]);

        $response->assertJsonStructure(['status', 'message', 'errors']);
        $response->assertStatus(422)
            ->assertJsonPath('errors.filter.0', trans('cuisine.filter.string'));
    }

    public function testIndexWithFilterAsObjectRejectsWithValidationError(): void
    {
        $response = $this->getAuthenticated(self::API_ENDPOINT, ['filter' => (object) ['value' => 'vegan']]);

        $response->assertJsonStructure(['status', 'message', 'errors']);
        $response->assertStatus(422)
            ->assertJsonPath('errors.filter.0', trans('cuisine.filter.string'));
    }

    public function testResponseHasOnlyExpectedKeysAndDataStructure(): void
    {
        $cuisines = ['c1', 'c2'];
        $transformed = ['data' => ['c1', 'c2']];

        $this->mockRepositoryGetCuisines(null, null, $cuisines);
        $this->mockTransformerTransformSet($cuisines, $transformed);

        $response = $this->getAuthenticated(self::API_ENDPOINT);

        $response->assertStatus(200)
            ->assertJsonStructure(['data'])
            ->assertJsonCount(count($cuisines), 'data')
            ->assertExactJson($transformed);

        $json = $response->json();
        $this->assertEquals(['data'], array_keys($json));
    }

    public function testEmptyRepositoryResultsReturnEmptyDataArray(): void
    {

        $this->mockRepositoryGetCuisines(null, null, []);
        $this->mockTransformerTransformSet([], ['data' => []]);

        $response = $this->getAuthenticated(self::API_ENDPOINT);

        $response->assertStatus(200)
            ->assertExactJson(['data' => []]);
    }

    /**
     * Test the filter input is capitalized before passed to the repository.
     */
    #[DataProvider('filterCapitalizationProvider')]
    public function testFilterIsCapitalizedBeforePassingToRepository(string $inputFilter, string $expectedFilter): void
    {
        $this->repository->expects($this->once())
            ->method('getCuisines')
            ->with($expectedFilter, null)
            ->willReturn([]);

        $response = $this->getAuthenticated(self::API_ENDPOINT . '?filter=' . $inputFilter);

        $response->assertStatus(200);
    }

    /**
     * @return array<int, array>
     */
    public static function filterCapitalizationProvider(): array
    {
        return [
            ['vegan', 'Vegan'],
            ['chinese', 'Chinese'],
            ['italian', 'Italian'],
        ];
    }

    public function testRepositoryExceptionReturns500WithErrorMessage(): void
    {
        $this->repository->expects($this->once())
            ->method('getCuisines')
            ->willThrowException(new Exception('Repository failure'));

        $this->transformer->expects($this->never())
            ->method('transformSet');

        $response = $this->getAuthenticated(self::API_ENDPOINT);

        $response->assertStatus(500)
            ->assertJsonStructure(['message'])
            ->assertJsonFragment(['message' => 'Repository failure']);
    }

    public function testTransformerExceptionReturns500WithErrorMessage(): void
    {
        $this->repository->expects($this->once())
            ->method('getCuisines')
            ->willReturn(['c1']);

        $this->transformer->expects($this->once())
            ->method('transformSet')
            ->willThrowException(new Exception('Transformer failure'));

        $response = $this->getAuthenticated(self::API_ENDPOINT);

        $response->assertStatus(500)
            ->assertJsonStructure(['message'])
            ->assertJsonFragment(['message' => 'Transformer failure']);
    }

    #[DataProvider('disallowedHttpMethodsProvider')]
    public function testDisallowedHttpMethodsReturn405(string $method): void
    {
        $response = $this->getAuthenticated(self::API_ENDPOINT, [], $method);
        $response->assertMethodNotAllowed();
    }

    /**
     * @return array<int, array>
     */
    public static function disallowedHttpMethodsProvider(): array
    {
        return [
            ['POST'],
            ['PUT'],
            ['PATCH'],
            ['DELETE'],
            // ['HEAD'],
            // ['OPTIONS'],
        ];
    }

    /**
     * Test URL encoded query params behave as expected.
     */
    public function testUrlEncodedQueryParameters(): void
    {
        $filter = 'vegan';
        $limit = 10;
        $encodedFilter = urlencode($filter);
        $encodedLimit = urlencode((string) $limit);

        $this->mockRepositoryGetCuisines('Vegan', $limit, ['c1', 'c2']);
        $this->mockTransformerTransformSet(['c1', 'c2'], ['data' => ['c1', 'c2']]);

        $response = $this->getAuthenticated(self::API_ENDPOINT . "?filter=$encodedFilter&limit=$encodedLimit");

        $response->assertOk()
            ->assertExactJson(['data' => ['c1', 'c2']]);
    }
}

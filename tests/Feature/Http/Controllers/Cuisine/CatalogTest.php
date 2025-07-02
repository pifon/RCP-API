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
    use CreatesTestUser, DatabaseTransactions, WithFaker;

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

    protected function getAuthenticated(string $uri, array $params = [], string $method = 'GET', array $headers = []): TestResponse
    {
        $token = JWTAuth::fromUser($this->user);

        return $this->withHeaders(array_merge([
            'Authorization' => 'Bearer '.$token,
        ], $headers))->json($method, $uri, $params);
    }

    public function test_index_without_parameters_returns_success(): void
    {
        $this->getAuthenticated(self::API_ENDPOINT)->assertOk();
    }

    public function test_index_no_params_returns_default_limit(): void
    {
        $this->mockRepositoryGetCuisines(null, null, ['cuisine1', 'cuisine2']);
        $this->mockTransformerTransformSet(['cuisine1', 'cuisine2'], ['data' => ['cuisine1', 'cuisine2']]);

        $response = $this->getAuthenticated(self::API_ENDPOINT);

        $response->assertOk()
            ->assertExactJson([
                'data' => ['cuisine1', 'cuisine2'],
            ]);
    }

    public function test_index_applies_limit_parameter_correctly(): void
    {
        $limit = 5;

        $this->mockRepositoryGetCuisines(null, $limit, ['cuisine1', 'cuisine2']);
        $this->mockTransformerTransformSet(['cuisine1', 'cuisine2'], ['data' => ['cuisine1', 'cuisine2']]);

        $response = $this->getAuthenticated(self::API_ENDPOINT.'?limit='.$limit);

        $response->assertOk()
            ->assertExactJson([
                'data' => ['cuisine1', 'cuisine2'],
            ]);
    }

    #[DataProvider('invalidLimitProvider')]
    public function test_invalid_limits_return_validation_error(string $limit, string $expectedMessageKey): void
    {
        $response = $this->getAuthenticated(self::API_ENDPOINT."?limit=$limit");

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

    public function test_index_with_valid_filter_returns_status_ok(): void
    {
        $this->repository
            ->expects($this->once())
            ->method('getCuisines')
            ->with('Vegan', null)
            ->willReturn([]);

        $response = $this->getAuthenticated(self::API_ENDPOINT.'?filter=vegan');

        $response->assertStatus(200);
    }

    #[DataProvider('invalidFilterProvider')]
    public function test_invalid_filters_return_validation_error(mixed $filter, string $expectedMessageKey): void
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

    public function test_index_with_unexpected_field_throws_bad_request(): void
    {
        $response = $this->getAuthenticated(self::API_ENDPOINT.'?something=unexpected');

        $response->assertStatus(400)
            ->assertJsonStructure(['message', 'errors']);

        $this->assertJsonHasOnlyKeys(self::ERROR_KEYS, $response);
    }

    public function test_index_with_filter_and_limit_returns_status_ok(): void
    {
        $this->mockRepositoryGetCuisines('Vegan', 10, ['c1', 'c2']);
        $this->mockTransformerTransformSet(['c1', 'c2'], ['data' => ['c1', 'c2']]);

        $response = $this->getAuthenticated(self::API_ENDPOINT.'?filter=vegan&limit=10');

        $response->assertOk()
            ->assertJsonStructure(['data'])
            ->assertExactJson(['data' => ['c1', 'c2']]);

        $this->assertJsonHasOnlyKeys(self::DATA_KEYS, $response);
    }

    // Similarly for your other success tests returning JSON:
    public function test_index_response_contract_with_mocked_transformer(): void
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

    public function test_index_with_empty_filter_and_limit(): void
    {
        $response = $this->getAuthenticated(self::API_ENDPOINT.'?filter=&limit=');
        $response->assertStatus(422);
    }

    public function test_index_limit_at_minimum_boundary(): void
    {
        $this->mockRepositoryGetCuisines(null, 1, []);
        $this->mockTransformerTransformSet([], ['data' => []]);

        $response = $this->getAuthenticated(self::API_ENDPOINT.'?limit=1');

        $response->assertOk();
    }

    public function test_index_limit_at_maximum_boundary(): void
    {
        $this->mockRepositoryGetCuisines(null, 50, []);
        $this->mockTransformerTransformSet([], ['data' => []]);

        $response = $this->getAuthenticated(self::API_ENDPOINT.'?limit=50');

        $response->assertOk();
    }

    public function test_index_with_filter_as_array_rejects_with_validation_error(): void
    {
        $response = $this->getAuthenticated(self::API_ENDPOINT, ['filter' => ['vegan', 'vegetarian']]);

        $response->assertJsonStructure(['status', 'message', 'errors']);
        $response->assertStatus(422)
            ->assertJsonPath('errors.filter.0', trans('cuisine.filter.string'));
    }

    public function test_index_with_filter_as_object_rejects_with_validation_error(): void
    {
        $response = $this->getAuthenticated(self::API_ENDPOINT, ['filter' => (object) ['value' => 'vegan']]);

        $response->assertJsonStructure(['status', 'message', 'errors']);
        $response->assertStatus(422)
            ->assertJsonPath('errors.filter.0', trans('cuisine.filter.string'));
    }

    public function test_response_has_only_expected_keys_and_data_structure(): void
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

    public function test_empty_repository_results_return_empty_data_array(): void
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
    public function test_filter_is_capitalized_before_passing_to_repository(string $inputFilter, string $expectedFilter): void
    {
        $this->repository->expects($this->once())
            ->method('getCuisines')
            ->with($expectedFilter, null)
            ->willReturn([]);

        $response = $this->getAuthenticated(self::API_ENDPOINT.'?filter='.$inputFilter);

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

    public function test_repository_exception_returns_500_with_error_message(): void
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

    public function test_transformer_exception_returns_500_with_error_message(): void
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
    public function test_disallowed_http_methods_return_405(string $method): void
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
    public function test_url_encoded_query_parameters(): void
    {
        $filter = 'vegan';
        $limit = 10;
        $encodedFilter = urlencode($filter);
        $encodedLimit = urlencode((string) $limit);

        $this->mockRepositoryGetCuisines('Vegan', $limit, ['c1', 'c2']);
        $this->mockTransformerTransformSet(['c1', 'c2'], ['data' => ['c1', 'c2']]);

        $response = $this->getAuthenticated(self::API_ENDPOINT."?filter=$encodedFilter&limit=$encodedLimit");

        $response->assertOk()
            ->assertExactJson(['data' => ['c1', 'c2']]);
    }
}

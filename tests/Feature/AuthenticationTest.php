<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Entities\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Testing\TestResponse;
use Tests\Helpers\CreatesTestUser;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use CreatesTestUser;
    use DatabaseTransactions;

    private const string API_PROTECTED_ENDPOINT = '/api/v1/cuisines';

    private const string API_LOGIN_ENDPOINT = '/api/login';

    private const string USERNAME = 'test-user';

    private const string PASSWORD = 'Pa$swo[d_1234';

    private const string CT = 'application/vnd.api+json';

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createOrGetTestUser();
    }

    private function loginPost(array $data): TestResponse
    {
        return $this->call(
            'POST',
            self::API_LOGIN_ENDPOINT,
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => self::CT,
                'HTTP_ACCEPT' => self::CT,
            ],
            json_encode($data),
        );
    }

    public function testUserCanLoginWithValidUsernameAndPassword(): void
    {
        $response = $this->loginPost([
            'username' => self::USERNAME,
            'password' => self::PASSWORD,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'jsonapi',
                'meta' => ['access_token', 'token_type', 'expires_in'],
            ]);
    }

    public function testUserCannotLoginWithInvalidCredentials(): void
    {
        $response = $this->loginPost([
            'username' => self::USERNAME,
            'password' => 'wrong_password',
        ]);

        $response->assertStatus(401)
            ->assertJsonStructure(['jsonapi', 'errors'])
            ->assertJsonPath('errors.0.status', '401')
            ->assertJsonPath('errors.0.detail', 'Invalid credentials.');
    }

    public function testUserCannotLoginWithoutPasswordField(): void
    {
        $response = $this->loginPost([
            'username' => self::USERNAME,
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure(['jsonapi', 'errors'])
            ->assertJsonPath('errors.0.status', '422');
    }

    public function testUserCannotLoginWithoutUsernameField(): void
    {
        $response = $this->loginPost([
            'password' => self::PASSWORD,
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure(['jsonapi', 'errors'])
            ->assertJsonPath('errors.0.status', '422');
    }

    public function testUserCannotLoginWithEmptyUsernameField(): void
    {
        $response = $this->loginPost([
            'username' => '',
            'password' => self::PASSWORD,
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure(['jsonapi', 'errors'])
            ->assertJsonPath('errors.0.status', '422');
    }

    public function testUserCannotLoginWithTooLongUsernameField(): void
    {
        $response = $this->loginPost([
            'username' => str_repeat('a', 257),
            'password' => self::PASSWORD,
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure(['jsonapi', 'errors']);
    }

    public function testUserCannotLoginWithEmptyPasswordField(): void
    {
        $response = $this->loginPost([
            'username' => self::USERNAME,
            'password' => '',
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure(['jsonapi', 'errors']);
    }

    public function testUserCannotLoginWithTooLongPasswordField(): void
    {
        $response = $this->loginPost([
            'username' => self::USERNAME,
            'password' => str_repeat('0123456789', 50),
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure(['jsonapi', 'errors']);
    }

    public function testUserCannotLoginWithWildcardPasswordField(): void
    {
        $response = $this->loginPost([
            'username' => self::USERNAME,
            'password' => '%',
        ]);

        $response->assertStatus(401)
            ->assertJsonPath('errors.0.detail', 'Invalid credentials.');
    }

    public function testUserCannotLoginWithExtraUnexpectedField(): void
    {
        $response = $this->loginPost([
            'username' => self::USERNAME,
            'password' => self::PASSWORD,
            'extra_field' => 'foo',
        ]);

        $response->assertStatus(400)
            ->assertJsonStructure(['jsonapi', 'errors'])
            ->assertJsonPath('errors.0.status', '400');
    }

    public function testAuthenticatedUserCanAccessProtectedRoute(): void
    {
        $token = auth('api')->login($this->user);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
            'Accept' => self::CT,
        ])->getJson(self::API_PROTECTED_ENDPOINT);

        $response->assertStatus(200);
    }

    public function testBlockFakeBearerAccessToProtectedRoute(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer not-real',
            'Accept' => self::CT,
        ])->getJson(self::API_PROTECTED_ENDPOINT);

        $response->assertStatus(401)
            ->assertJsonStructure(['jsonapi', 'errors'])
            ->assertJsonPath('errors.0.status', '401')
            ->assertJsonPath('errors.0.title', 'Unauthorized');
    }

    public function testBlockAccessToProtectedRoute(): void
    {
        $response = $this->withHeaders([
            'Accept' => self::CT,
        ])->getJson(self::API_PROTECTED_ENDPOINT);

        $response->assertStatus(401)
            ->assertJsonStructure(['jsonapi', 'errors'])
            ->assertJsonPath('errors.0.status', '401')
            ->assertJsonPath('errors.0.title', 'Unauthorized');
    }
}

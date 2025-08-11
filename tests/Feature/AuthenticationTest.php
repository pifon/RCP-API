<?php

namespace Tests\Feature;

use App\Entities\User;
use App\Exceptions\v1\ValidationErrorException;
use App\Repositories\v1\UserRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Helpers\CreatesTestUser;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use CreatesTestUser, DatabaseTransactions;

    private const string API_PROTECTED_ENDPOINT = '/api/v1/cuisines';

    private const string API_LOGIN_ENDPOINT = '/api/login';

    private const string USERNAME = 'test-user';

    private const string PASSWORD = 'Pa$swo[d_1234';

    protected UserRepository $userRepository;

    private User $user;

    /**
     * @throws ValidationErrorException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createOrGetTestUser();
    }

    public function test_user_can_login_with_valid_username_and_password(): void
    {
        $response = $this->postJson(self::API_LOGIN_ENDPOINT, [
            'username' => self::USERNAME,
            'password' => self::PASSWORD,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'access_token',
            'token_type',
            'expires_in',
        ]);
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        $response = $this->postJson(self::API_LOGIN_ENDPOINT, [
            'username' => self::USERNAME,
            'password' => 'wrong_password',
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Invalid credentials.',
        ]);
    }

    public function test_user_cannot_login_without_password_field(): void
    {
        $response = $this->postJson(self::API_LOGIN_ENDPOINT, [
            'username' => self::USERNAME,
        ]);

        $response->assertJsonStructure(['message', 'errors']);
        $response->assertStatus(422)
            ->assertJsonPath('errors.password.0', trans('auth.password.required'))
            ->assertJsonPath('message', trans('auth.password.required'));
    }

    public function test_user_cannot_login_without_username_field(): void
    {
        $response = $this->postJson(self::API_LOGIN_ENDPOINT, [
            'password' => self::PASSWORD,
        ]);

        $response->assertJsonStructure(['message', 'errors']);
        $response->assertStatus(422)
            ->assertJsonPath('errors.username.0', trans('auth.username.required'))
            ->assertJsonPath('message', trans('auth.username.required'));
    }

    public function test_user_cannot_login_with_non_string_username_field(): void
    {
        $response = $this->postJson(self::API_LOGIN_ENDPOINT, [
            'username' => (object) [0 => '%', 1 => '*'],
            'password' => self::PASSWORD,
        ]);

        $response->assertJsonStructure(['message', 'errors']);
        $response->assertStatus(422)
            ->assertJsonPath('errors.username.0', trans('auth.username.string'))
            ->assertJsonPath('message', trans('auth.username.string'));
    }

    public function test_user_cannot_login_with_extra_unexpected_field(): void
    {
        $response = $this->postJson(self::API_LOGIN_ENDPOINT, [
            'username' => self::USERNAME,
            'password' => self::PASSWORD,
            'extra_field' => 'foo',
        ]);

        $response->assertStatus(400)
            ->assertJsonStructure(['status', 'message', 'errors'])
            ->assertJsonPath('errors.0', trans('auth.unexpected_fields.error'))
            ->assertJsonPath('message', trans('auth.unexpected_fields.message'));
    }

    public function test_user_cannot_login_with_empty_username_field(): void
    {
        $response = $this->postJson(self::API_LOGIN_ENDPOINT, [
            'username' => '',
            'password' => self::PASSWORD,
        ]);

        $response->assertJsonStructure(['message', 'errors']);
        $response->assertStatus(422)
            ->assertJsonPath('errors.username.0', trans('auth.username.required'))
            ->assertJsonPath('message', trans('auth.username.required'));
    }

    public function test_user_cannot_login_with_too_long_username_field(): void
    {
        $response = $this->postJson(self::API_LOGIN_ENDPOINT, [
            'username' => str_repeat('a', 257),
            'password' => self::PASSWORD,
        ]);

        $response->assertJsonStructure(['message', 'errors']);
        $response->assertStatus(422)
            ->assertJsonPath('errors.username.0', trans('auth.username.max'))
            ->assertJsonPath('message', trans('auth.username.max'));
    }

    public function test_user_cannot_login_with_non_string_password_field(): void
    {
        $response = $this->postJson(self::API_LOGIN_ENDPOINT, [
            'username' => self::USERNAME,
            'password' => (object) [0 => '%', 1 => '*'],
        ]);

        $response->assertJsonStructure(['message', 'errors']);
        $response->assertStatus(422)
            ->assertJsonPath('errors.password.0', trans('auth.password.string'))
            ->assertJsonPath('message', trans('auth.password.string'));
    }

    public function test_user_cannot_login_with_wildcard_password_field(): void
    {
        $response = $this->postJson(self::API_LOGIN_ENDPOINT, [
            'username' => self::USERNAME,
            'password' => '%',
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Invalid credentials.',
        ]);
    }

    public function test_user_cannot_login_with_empty_password_field(): void
    {
        $response = $this->postJson(self::API_LOGIN_ENDPOINT, [
            'username' => self::USERNAME,
            'password' => '',
        ]);

        $response->assertJsonStructure(['message', 'errors']);
        $response->assertStatus(422)
            ->assertJsonPath('errors.password.0', trans('auth.password.required'))
            ->assertJsonPath('message', trans('auth.password.required'));
    }

    public function test_user_cannot_login_with_too_long_password_field(): void
    {
        $response = $this->postJson(self::API_LOGIN_ENDPOINT, [
            'username' => self::USERNAME,
            'password' => str_repeat('0123456789', 50),
        ]);

        $response->assertJsonStructure(['message', 'errors']);
        $response->assertStatus(422)
            ->assertJsonPath('errors.password.0', trans('auth.password.max'))
            ->assertJsonPath('message', trans('auth.password.max'));
    }

    public function test_authenticated_user_can_access_protected_route(): void
    {
        $token = auth('api')->login($this->user);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson(self::API_PROTECTED_ENDPOINT);

        $response->assertStatus(200);
    }

    public function test_block_fake_bearer_access_to_protected_route(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer not-real')
            ->getJson(self::API_PROTECTED_ENDPOINT);

        $response->assertStatus(401);
        $response->assertJson([
            'errors' => [[
                'title' => 'Unauthenticated',
                'code' => '401',
                'detail' => 'You must login to access this resource',
            ]],
        ]);
    }

    public function test_block_access_to_protected_route(): void
    {
        $response = $this->getJson(self::API_PROTECTED_ENDPOINT);

        $response->assertStatus(401);
        $response->assertJson([
            'errors' => [[
                'title' => 'Unauthenticated',
                'code' => '401',
                'detail' => 'You must login to access this resource',
            ]],
        ]);
    }
}

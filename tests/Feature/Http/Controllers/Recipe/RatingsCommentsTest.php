<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Recipe;

use App\Entities\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Helpers\CreatesTestUser;
use Tests\Helpers\JsonApiRequests;
use Tests\TestCase;

class RatingsCommentsTest extends TestCase
{
    use CreatesTestUser;
    use DatabaseTransactions;
    use JsonApiRequests;

    private User $user;

    private string $token;

    private const string RECIPE_SLUG = 'pizza';

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

    // ── Ratings ──────────────────────────────────────────────────

    public function test_list_ratings_for_recipe(): void
    {
        $response = $this->apiGet('/api/v1/recipes/'.self::RECIPE_SLUG.'/ratings');

        $response->assertOk()
            ->assertJsonStructure(['jsonapi', 'data']);
    }

    public function test_create_or_update_rating(): void
    {
        $response = $this->apiPost('/api/v1/recipes/'.self::RECIPE_SLUG.'/ratings', [
            'data' => [
                'type' => 'ratings',
                'attributes' => ['rate' => 4],
            ],
        ]);

        $this->assertContains($response->status(), [200, 201]);
        $response->assertJsonPath('data.type', 'ratings');
    }

    // ── Comments ─────────────────────────────────────────────────

    public function test_list_comments_for_recipe(): void
    {
        $response = $this->apiGet('/api/v1/recipes/'.self::RECIPE_SLUG.'/comments');

        $response->assertOk()
            ->assertJsonStructure(['jsonapi', 'data']);
    }

    public function test_create_comment(): void
    {
        $response = $this->apiPost('/api/v1/recipes/'.self::RECIPE_SLUG.'/comments', [
            'data' => [
                'type' => 'comments',
                'attributes' => ['body' => 'Great recipe, loved it!'],
            ],
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.type', 'comments');
    }

    // ── Activity ─────────────────────────────────────────────────

    public function test_log_activity(): void
    {
        $response = $this->apiPost('/api/v1/recipes/'.self::RECIPE_SLUG.'/activity', [
            'data' => [
                'type' => 'activities',
                'attributes' => ['action' => 'cooked'],
            ],
        ]);

        $response->assertStatus(201);
    }
}

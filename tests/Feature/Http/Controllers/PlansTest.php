<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Entities\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Helpers\CreatesTestUser;
use Tests\Helpers\JsonApiRequests;
use Tests\TestCase;

class PlansTest extends TestCase
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

    public function test_list_plans(): void
    {
        $response = $this->apiGet('/api/v1/plans');

        $response->assertOk()
            ->assertJsonStructure(['jsonapi', 'data']);
    }

    public function test_show_plan_by_slug(): void
    {
        $list = $this->apiGet('/api/v1/plans');
        $list->assertOk();

        $data = $list->json('data');
        if (count($data) === 0) {
            $this->markTestSkipped('No plans seeded');
        }

        $slug = $data[0]['id'];
        $response = $this->apiGet("/api/v1/plans/{$slug}");

        $response->assertOk()
            ->assertJsonPath('data.type', 'plans')
            ->assertJsonPath('data.id', $slug);
    }

    public function test_show_plan_returns404_for_missing(): void
    {
        $response = $this->apiGet('/api/v1/plans/nonexistent-plan-xyz');

        $response->assertNotFound();
    }

    public function test_me_subscription_returns_valid_response(): void
    {
        $response = $this->apiGet('/api/v1/me/subscription');

        $response->assertOk()
            ->assertJsonStructure(['jsonapi']);
    }
}

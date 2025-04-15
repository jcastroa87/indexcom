<?php

namespace Tests\Feature\Api;

use App\Models\Index;
use App\Models\Rate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Index $index;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a test user with API key
        $this->user = User::factory()->create([
            'api_key' => 'test-api-key-12345',
            'is_active' => true,
        ]);

        // Create a test index
        $this->index = Index::create([
            'name' => 'Test Index',
            'slug' => 'test-index',
            'description' => 'An index for testing purposes',
            'is_active' => true,
            'fetch_frequency' => 60,
        ]);

        // Create some test rates
        $dates = [
            now()->subDays(3),
            now()->subDays(2),
            now()->subDays(1),
            now(),
        ];

        foreach ($dates as $i => $date) {
            Rate::create([
                'index_id' => $this->index->id,
                'date' => $date,
                'value' => 100 + ($i * 2), // 100, 102, 104, 106
                'is_manual' => true,
            ]);
        }
    }

    /** @test */
    public function it_can_list_all_indices()
    {
        $response = $this->getJson('/api/indices');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'slug', 'description'],
                ],
                'meta' => ['total', 'timestamp'],
            ])
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', 'test-index');
    }

    /** @test */
    public function it_can_get_a_specific_index()
    {
        $response = $this->getJson('/api/indices/test-index');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['id', 'name', 'slug', 'description'],
                'meta' => ['timestamp'],
            ])
            ->assertJsonPath('data.slug', 'test-index');
    }

    /** @test */
    public function it_returns_404_for_non_existent_index()
    {
        $response = $this->getJson('/api/indices/non-existent');

        $response->assertStatus(404);
    }

    /** @test */
    public function it_can_get_rates_for_an_index()
    {
        $response = $this->getJson('/api/indices/test-index/rates');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['date', 'value'],
                ],
                'meta' => ['index', 'slug', 'count', 'timestamp'],
            ])
            ->assertJsonCount(4, 'data')
            ->assertJsonPath('meta.slug', 'test-index');
    }

    /** @test */
    public function it_can_filter_rates_by_date_range()
    {
        $response = $this->getJson('/api/indices/test-index/rates?from=' . now()->subDays(2)->format('Y-m-d'));

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');

        $response = $this->getJson('/api/indices/test-index/rates?to=' . now()->subDays(2)->format('Y-m-d'));

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    /** @test */
    public function it_can_get_latest_rate_for_an_index()
    {
        $response = $this->getJson('/api/indices/test-index/latest');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['date', 'value'],
                'meta' => ['index', 'slug', 'timestamp'],
            ])
            ->assertJsonPath('meta.slug', 'test-index')
            ->assertJsonPath('data.value', 106);
    }

    /** @test */
    public function it_requires_api_key_for_authenticated_endpoints()
    {
        $response = $this->getJson('/api/user');

        $response->assertStatus(401)
            ->assertJson([
                'error' => 'API key is missing',
            ]);
    }

    /** @test */
    public function it_validates_api_key_for_authenticated_endpoints()
    {
        $response = $this->getJson('/api/user', [
            'X-API-Key' => 'invalid-key',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'error' => 'Invalid or inactive API key',
            ]);
    }

    /** @test */
    public function it_allows_access_with_valid_api_key()
    {
        $response = $this->getJson('/api/user', [
            'X-API-Key' => 'test-api-key-12345',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('id', $this->user->id)
            ->assertJsonPath('email', $this->user->email);
    }
}

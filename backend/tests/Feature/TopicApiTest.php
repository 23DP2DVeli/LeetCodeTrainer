<?php

namespace Tests\Feature;

use App\Models\Topic;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TopicApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_topics(): void
    {
        Topic::create(['name' => 'Array', 'slug' => 'array']);
        Topic::create(['name' => 'Dynamic Programming', 'slug' => 'dynamic-programming']);
        Topic::create(['name' => 'Binary Search', 'slug' => 'binary-search']);

        $response = $this->getJson('/api/topics');

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => ['id', 'name', 'slug', 'description', 'created_at', 'updated_at'],
            ])
            ->assertJsonCount(3);
    }

    public function test_topics_are_ordered_by_name(): void
    {
        Topic::create(['name' => 'String', 'slug' => 'string']);
        Topic::create(['name' => 'Array', 'slug' => 'array']);
        Topic::create(['name' => 'Math', 'slug' => 'math']);

        $response = $this->getJson('/api/topics');

        $response->assertStatus(200)
            ->assertJsonPath('0.name', 'Array')
            ->assertJsonPath('1.name', 'Math')
            ->assertJsonPath('2.name', 'String');
    }
}

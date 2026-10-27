<?php

namespace Tests\Feature;

use App\Models\Problem;
use App\Models\Topic;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProblemApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_problems(): void
    {
        Problem::create([
            'title' => 'Two Sum',
            'slug' => 'two-sum',
            'difficulty' => 'Easy',
            'acceptance_rate' => 49.50,
        ]);

        Problem::create([
            'title' => 'Add Two Numbers',
            'slug' => 'add-two-numbers',
            'difficulty' => 'Medium',
            'acceptance_rate' => 42.30,
        ]);

        $response = $this->getJson('/api/problems');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'slug', 'difficulty', 'acceptance_rate', 'topics'],
                ],
                'current_page',
                'per_page',
                'total',
            ])
            ->assertJsonCount(2, 'data');
    }

    public function test_problems_include_related_topics(): void
    {
        $problem = Problem::create([
            'title' => 'Two Sum',
            'slug' => 'two-sum',
            'difficulty' => 'Easy',
        ]);

        $topic = Topic::create([
            'name' => 'Array',
            'slug' => 'array',
        ]);

        $problem->topics()->attach($topic->id, ['relevance_weight' => 1.00]);

        $response = $this->getJson('/api/problems');

        $response->assertStatus(200)
            ->assertJsonPath('data.0.topics.0.name', 'Array');
    }
}

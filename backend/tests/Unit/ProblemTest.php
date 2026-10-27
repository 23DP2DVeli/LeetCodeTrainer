<?php

namespace Tests\Unit;

use App\Models\Problem;
use App\Models\Topic;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProblemTest extends TestCase
{
    use RefreshDatabase;

    public function test_problem_has_topics_relationship(): void
    {
        $problem = Problem::create([
            'title' => 'Two Sum',
            'slug' => 'two-sum',
            'difficulty' => 'Easy',
            'acceptance_rate' => 49.50,
        ]);

        $topic = Topic::create([
            'name' => 'Array',
            'slug' => 'array',
        ]);

        $problem->topics()->attach($topic->id, ['relevance_weight' => 1.00]);

        $this->assertCount(1, $problem->topics);
        $this->assertEquals('Array', $problem->topics->first()->name);
        $this->assertEquals(1.00, $problem->topics->first()->pivot->relevance_weight);
    }

    public function test_problem_casts_json_fields(): void
    {
        $problem = Problem::create([
            'title' => 'Test Problem',
            'slug' => 'test-problem',
            'difficulty' => 'Medium',
            'hints' => ['Hint 1', 'Hint 2'],
            'metadata' => ['companies' => ['Google', 'Amazon']],
        ]);

        $this->assertIsArray($problem->hints);
        $this->assertIsArray($problem->metadata);
        $this->assertEquals(['Hint 1', 'Hint 2'], $problem->hints);
        $this->assertEquals(['companies' => ['Google', 'Amazon']], $problem->metadata);
    }
}

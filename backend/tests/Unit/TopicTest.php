<?php

namespace Tests\Unit;

use App\Models\Problem;
use App\Models\Topic;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TopicTest extends TestCase
{
    use RefreshDatabase;

    public function test_topic_has_problems_relationship(): void
    {
        $topic = Topic::create([
            'name' => 'Dynamic Programming',
            'slug' => 'dynamic-programming',
        ]);

        $problem = Problem::create([
            'title' => 'Climbing Stairs',
            'slug' => 'climbing-stairs',
            'difficulty' => 'Easy',
        ]);

        $topic->problems()->attach($problem->id, ['relevance_weight' => 0.95]);

        $this->assertCount(1, $topic->problems);
        $this->assertEquals('Climbing Stairs', $topic->problems->first()->title);
        $this->assertEquals(0.95, $topic->problems->first()->pivot->relevance_weight);
    }
}

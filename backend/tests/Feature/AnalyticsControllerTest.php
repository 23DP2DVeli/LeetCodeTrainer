<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Submission;

class AnalyticsControllerTest extends TestCase
{

    use RefreshDatabase;
    public function test_it_returns_user_analytics(): void
{
    $user = User::factory()->create([
        'leetcode_username' => 'testuser',
    ]);

    Submission::factory()->create([
        'user_id' => $user->id,
        'status' => 'Accepted',
        'language' => 'javascript',
    ]);

    $response = $this->getJson('/api/users/testuser/analytics');

    $response->assertOk()
        ->assertJsonStructure([
            'overview',
            'difficulty_breakdown',
            'daily_activity',
            'language_breakdown',
        ]);
}


public function test_it_returns_404_for_unknown_user(): void
{
    $response = $this->getJson('/api/users/unknown/analytics');

    $response->assertNotFound();
}
}

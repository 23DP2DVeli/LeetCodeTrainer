<?php

namespace Tests\Feature;

use App\Models\Submission;
use App\Models\User;
use App\Services\SubmissionAnalyticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubmissionAnalyticsServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_calculates_user_submission_overview(): void
    {
        $user = User::factory()->create([
            'leetcode_username' => 'test_user',
        ]);

        Submission::factory()->create([
            'user_id' => $user->id,
            'problem_slug' => 'two-sum',
            'status' => 'Accepted',
        ]);

        Submission::factory()->create([
            'user_id' => $user->id,
            'problem_slug' => 'two-sum',
            'status' => 'Wrong Answer',
        ]);

        Submission::factory()->create([
            'user_id' => $user->id,
            'problem_slug' => 'valid-anagram',
            'status' => 'Accepted',
        ]);

        $overview = app(SubmissionAnalyticsService::class)
            ->getOverview($user);

        $this->assertSame(3, $overview['total_submissions']);
        $this->assertSame(2, $overview['accepted_submissions']);
        $this->assertSame(1, $overview['failed_submissions']);
        $this->assertSame(2, $overview['solved_problems']);
        $this->assertSame(66.67, $overview['success_rate']);
    }

    public function test_it_returns_zeroes_when_user_has_no_submissions(): void
    {
        $user = User::factory()->create();

        $overview = app(SubmissionAnalyticsService::class)
            ->getOverview($user);

        $this->assertSame(0, $overview['total_submissions']);
        $this->assertSame(0, $overview['accepted_submissions']);
        $this->assertSame(0, $overview['failed_submissions']);
        $this->assertSame(0, $overview['solved_problems']);
        $this->assertSame(0.0, $overview['success_rate']);
    }

    public function test_it_does_not_count_another_users_submissions(): void
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();

        Submission::factory()->create([
            'user_id' => $anotherUser->id,
            'problem_slug' => 'two-sum',
            'status' => 'Accepted',
        ]);

        $overview = app(SubmissionAnalyticsService::class)
            ->getOverview($user);

        $this->assertSame(0, $overview['total_submissions']);
        $this->assertSame(0, $overview['solved_problems']);
    }



public function test_it_calculates_solved_problems_by_difficulty(): void
{
    $user = User::factory()->create();

    $easy = \App\Models\Problem::factory()->create([
        'difficulty' => 'Easy',
    ]);

    $medium = \App\Models\Problem::factory()->create([
        'difficulty' => 'Medium',
    ]);

    $hard = \App\Models\Problem::factory()->create([
        'difficulty' => 'Hard',
    ]);

    Submission::factory()->create([
        'user_id' => $user->id,
        'problem_id' => $easy->id,
        'status' => 'Accepted',
    ]);

    Submission::factory()->create([
        'user_id' => $user->id,
        'problem_id' => $easy->id,
        'status' => 'Accepted',
    ]);

    Submission::factory()->create([
        'user_id' => $user->id,
        'problem_id' => $medium->id,
        'status' => 'Accepted',
    ]);

    Submission::factory()->create([
        'user_id' => $user->id,
        'problem_id' => $hard->id,
        'status' => 'Wrong Answer',
    ]);

    $result = app(SubmissionAnalyticsService::class)
        ->getDifficultyBreakdown($user);

    $this->assertSame([
        'Easy' => 1,
        'Medium' => 1,
        'Hard' => 0,
    ], $result);
}

public function test_it_returns_zeroes_when_user_has_no_solved_problems(): void
{
    $user = User::factory()->create();

    $result = app(SubmissionAnalyticsService::class)
        ->getDifficultyBreakdown($user);

    $this->assertSame([
        'Easy' => 0,
        'Medium' => 0,
        'Hard' => 0,
    ], $result);
}


public function test_it_returns_30_days_of_daily_activity(): void
{
    $user = User::factory()->create();

    $problem = \App\Models\Problem::factory()->create();

    Submission::factory()->create([
        'user_id' => $user->id,
        'problem_id' => $problem->id,
        'problem_slug' => $problem->slug,
        'status' => 'Accepted',
        'submitted_at' => now()->startOfDay(),
    ]);

    Submission::factory()->create([
        'user_id' => $user->id,
        'problem_id' => $problem->id,
        'problem_slug' => $problem->slug,
        'status' => 'Wrong Answer',
        'submitted_at' => now()->startOfDay(),
    ]);

    $activity = app(SubmissionAnalyticsService::class)
        ->getDailyActivity($user);

    $this->assertCount(30, $activity);

    $today = collect($activity)->last();

    $this->assertSame(now()->toDateString(), $today['date']);
    $this->assertSame(2, $today['submissions']);
    $this->assertSame(1, $today['solved_problems']);
}

public function test_it_returns_zero_activity_for_empty_days(): void
{
    $user = User::factory()->create();

    $activity = app(SubmissionAnalyticsService::class)
        ->getDailyActivity($user);

    $this->assertCount(30, $activity);

    foreach ($activity as $day) {
        $this->assertSame(0, $day['submissions']);
        $this->assertSame(0, $day['solved_problems']);
    }
}


}

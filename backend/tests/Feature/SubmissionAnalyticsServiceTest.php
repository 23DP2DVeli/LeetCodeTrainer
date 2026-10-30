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
}

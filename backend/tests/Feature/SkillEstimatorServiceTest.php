<?php

namespace Tests\Feature;

use App\Models\Problem;
use App\Models\Submission;
use App\Models\Topic;
use App\Models\User;
use App\Services\SkillEstimatorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SkillEstimatorServiceTest extends TestCase
{
    use RefreshDatabase;

    private SkillEstimatorService $estimator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->estimator = app(SkillEstimatorService::class);
    }

    public function test_topics_without_attempts_receive_neutral_prior(): void
    {
        $user = User::factory()->create();
$topic = Topic::query()->create([
    'name' => 'Arrays',
    'slug' => 'arrays',
    'description' => 'Array-related problems',
]);
        $result = $this->estimator->estimate($user->id);

        $stats = $result->firstWhere('topic_id', $topic->id);

        $this->assertNotNull($stats);
        $this->assertSame(0.5, $stats['skill_score']);
        $this->assertSame(0, $stats['attempts']);
        $this->assertSame(0.0, $stats['weighted_attempts']);
    }

    public function test_accepted_submission_increases_skill_score(): void
    {
        [$user, $topic, $problem] = $this->createProblemWithTopic();

        $this->createSubmission($user, $problem, 'Accepted');

        $stats = $this->getTopicStats($user, $topic);

        $this->assertSame(0.6, $stats['skill_score']);
        $this->assertSame(1, $stats['attempts']);
    }

    public function test_failed_submission_decreases_skill_score(): void
    {
        [$user, $topic, $problem] = $this->createProblemWithTopic();

        $this->createSubmission($user, $problem, 'Wrong Answer');

        $stats = $this->getTopicStats($user, $topic);

        $this->assertSame(0.4, $stats['skill_score']);
        $this->assertSame(1, $stats['attempts']);
    }

    public function test_submission_with_half_relevance_has_half_weight(): void
    {
        [$user, $topic, $problem] = $this->createProblemWithTopic(0.5);

        $this->createSubmission($user, $problem, 'Accepted');

        $stats = $this->getTopicStats($user, $topic);

        // (2 + 0.5) / (2 + 2 + 0.5) = 0.5556
        $this->assertSame(0.5556, $stats['skill_score']);
        $this->assertSame(0.5, $stats['weighted_attempts']);
    }

    public function test_attempts_from_another_user_do_not_affect_skill_score(): void
    {
        [$user, $topic, $problem] = $this->createProblemWithTopic();

        $anotherUser = User::factory()->create();

        $this->createSubmission($anotherUser, $problem, 'Accepted');

        $stats = $this->getTopicStats($user, $topic);

        $this->assertSame(0.5, $stats['skill_score']);
        $this->assertSame(0, $stats['attempts']);
    }

    private function createProblemWithTopic(float $relevance = 1.0): array
{
    $user = User::factory()->create();

    $topic = Topic::query()->create([
        'name' => 'Arrays',
        'slug' => 'arrays',
        'description' => 'Array-related problems',
    ]);

    $problem = Problem::factory()->create();

    $problem->topics()->attach($topic->id, [
        'relevance_weight' => $relevance,
    ]);

    return [$user, $topic, $problem];
}

    private function createSubmission(
        User $user,
        Problem $problem,
        string $status
    ): Submission {
        return Submission::factory()->create([
            'user_id' => $user->id,
            'problem_id' => $problem->id,
            'problem_slug' => $problem->slug,
            'status' => $status,
            'submitted_at' => now(),
        ]);
    }

    private function getTopicStats(User $user, Topic $topic): array
    {
        return $this->estimator
            ->estimate($user->id)
            ->firstWhere('topic_id', $topic->id);

            }

public function test_attempt_weight_halves_after_thirty_days(): void
{
    [$user, $topic, $problem] = $this->createProblemWithTopic();

    $this->createSubmission(
        $user,
        $problem,
        'Accepted'
    );

    $this->travel(30)->days();

    $stats = $this->getTopicStats($user, $topic);

    $this->assertSame(0.5556, $stats['skill_score']);
    $this->assertSame(0.5, $stats['weighted_attempts']);
}

public function test_recent_success_has_more_influence_than_old_failure(): void
{
    [$user, $topic, $problem] = $this->createProblemWithTopic();

    $this->createSubmission(
        $user,
        $problem,
        'Wrong Answer'
    );

    $this->travel(30)->days();

    $this->createSubmission(
        $user,
        $problem,
        'Accepted'
    );

    $stats = $this->getTopicStats($user, $topic);

    // Old failure weight = 0.5, recent success weight = 1.0.
    // Score = (2 + 1) / (4 + 0.5 + 1) = 0.5455.
    $this->assertSame(0.5455, $stats['skill_score']);
    $this->assertSame(2, $stats['attempts']);
    $this->assertSame(1.5, $stats['weighted_attempts']);
}


            }

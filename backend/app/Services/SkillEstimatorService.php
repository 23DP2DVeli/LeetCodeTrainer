<?php

namespace App\Services;

use App\Models\Submission;
use App\Models\Topic;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SkillEstimatorService
{
    private const PRIOR_ALPHA = 2.0;
    private const PRIOR_BETA = 2.0;
    private const HALF_LIFE_DAYS = 30.0;

    /**
     * Estimate the user's skill level for every available topic.
     */
    public function estimate(int $userId): Collection
    {
        $topicStats = Topic::query()
            ->get()
            ->mapWithKeys(function (Topic $topic) {
                return [
                    $topic->id => [
                        'topic_id' => $topic->id,
                        'topic_name' => $topic->name,
                        'alpha' => self::PRIOR_ALPHA,
                        'beta' => self::PRIOR_BETA,
                        'attempts' => 0,
                        'weighted_attempts' => 0.0,
                    ],
                ];
            })
            ->all();

        $submissions = Submission::query()
            ->where('user_id', $userId)
            ->whereNotNull('problem_id')
            ->with('problem.topics')
            ->orderBy('submitted_at')
            ->get();

        foreach ($submissions as $submission) {
            if (!$submission->problem) {
                continue;
            }

            $daysAgo = max(
                0,
                Carbon::parse($submission->submitted_at)->diffInDays(now())
            );

            $recencyWeight = pow(
                2,
                -$daysAgo / self::HALF_LIFE_DAYS
            );

            $result = $submission->isAccepted() ? 1.0 : 0.0;

            foreach ($submission->problem->topics as $topic) {
                $relevance = (float) $topic->pivot->relevance_weight;

                if ($relevance <= 0 || $relevance > 1) {
                    continue;
                }

                $topicId = $topic->id;

                if (!isset($topicStats[$topicId])) {
                    continue;
                }

                $weight = $recencyWeight * $relevance;

                $topicStats[$topicId]['alpha'] += $weight * $result;
                $topicStats[$topicId]['beta'] += $weight * (1 - $result);
                $topicStats[$topicId]['attempts']++;
                $topicStats[$topicId]['weighted_attempts'] += $weight;
            }
        }

        return collect($topicStats)
            ->map(function (array $stats) {
                $total = $stats['alpha'] + $stats['beta'];

                return [
                    'topic_id' => $stats['topic_id'],
                    'topic_name' => $stats['topic_name'],
                    'skill_score' => round(
                        $stats['alpha'] / $total,
                        4
                    ),
                    'attempts' => $stats['attempts'],
                    'weighted_attempts' => round(
                        $stats['weighted_attempts'],
                        4
                    ),
                ];
            })
            ->sortByDesc('skill_score')
            ->values();
    }
}

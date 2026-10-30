<?php

namespace App\Services;

use App\Models\Submission;
use App\Models\User;

class SubmissionAnalyticsService
{
    public function getOverview(User $user): array
    {
        $query = Submission::query()
            ->where('user_id', $user->id);

        $totalSubmissions = (clone $query)->count();

        $acceptedSubmissions = (clone $query)
            ->where('status', 'Accepted')
            ->count();

        $failedSubmissions = (clone $query)
            ->where('status', '!=', 'Accepted')
            ->count();

        $solvedProblems = (clone $query)
            ->where('status', 'Accepted')
            ->whereNotNull('problem_slug')
            ->distinct('problem_slug')
            ->count('problem_slug');

        $successRate = $totalSubmissions > 0
            ? round(($acceptedSubmissions / $totalSubmissions) * 100, 2)
            : 0.0;

        return [
            'total_submissions' => $totalSubmissions,
            'accepted_submissions' => $acceptedSubmissions,
            'failed_submissions' => $failedSubmissions,
            'solved_problems' => $solvedProblems,
            'success_rate' => $successRate,
        ];
    }
}

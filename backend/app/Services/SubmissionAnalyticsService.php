<?php

namespace App\Services;

use App\Models\Submission;
use App\Models\User;
use Carbon\Carbon;

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



public function getDifficultyBreakdown(User $user): array
{
    $counts = Submission::query()
        ->join('problems', 'submissions.problem_id', '=', 'problems.id')
        ->where('submissions.user_id', $user->id)
        ->where('submissions.status', 'Accepted')
        ->selectRaw('problems.difficulty, COUNT(DISTINCT problems.id) as solved')
        ->groupBy('problems.difficulty')
        ->pluck('solved', 'difficulty');

    return [
        'Easy' => (int) ($counts['Easy'] ?? 0),
        'Medium' => (int) ($counts['Medium'] ?? 0),
        'Hard' => (int) ($counts['Hard'] ?? 0),
    ];
}




public function getDailyActivity(User $user): array
{
    $startDate = Carbon::today()->subDays(29);
    $endDate = Carbon::today();

    $submissions = Submission::query()
        ->where('user_id', $user->id)
        ->whereBetween('submitted_at', [
            $startDate->copy()->startOfDay(),
            $endDate->copy()->endOfDay(),
        ])
        ->selectRaw('DATE(submitted_at) as date, COUNT(*) as total')
        ->groupBy('date')
        ->pluck('total', 'date');

    $accepted = Submission::query()
        ->where('user_id', $user->id)
        ->where('status', 'Accepted')
        ->whereNotNull('problem_id')
        ->whereBetween('submitted_at', [
            $startDate->copy()->startOfDay(),
            $endDate->copy()->endOfDay(),
        ])
        ->selectRaw('DATE(submitted_at) as date, COUNT(DISTINCT problem_id) as total')
        ->groupBy('date')
        ->pluck('total', 'date');

    $activity = [];

    for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
        $key = $date->toDateString();

        $activity[] = [
            'date' => $key,
            'submissions' => (int) ($submissions[$key] ?? 0),
            'solved_problems' => (int) ($accepted[$key] ?? 0),
        ];
    }

    return $activity;
}


}

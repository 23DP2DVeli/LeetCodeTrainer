<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SubmissionAnalyticsService;
use Illuminate\Http\JsonResponse;

class AnalyticsController extends Controller
{
    public function __construct(
        private SubmissionAnalyticsService $analyticsService
    ) {}

    public function show(string $username): JsonResponse
    {
            $user = User::where('leetcode_username', $username)->firstOrFail();
        return response()->json([
            'overview' => $this->analyticsService->getOverview($user),
            'difficulty_breakdown' => $this->analyticsService->getDifficultyBreakdown($user),
            'daily_activity' => $this->analyticsService->getDailyActivity($user),
            'language_breakdown' => $this->analyticsService->getLanguageBreakdown($user),
        ]);
    }
}

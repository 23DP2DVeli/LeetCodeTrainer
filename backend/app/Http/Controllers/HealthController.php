<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class HealthController extends Controller
{
    /**
     * Health check endpoint
     */
    public function check(): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'service' => 'LeetCodeTrainer API',
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}

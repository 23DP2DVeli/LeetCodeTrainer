<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class LeetCodeService
{
    public function getSkillStats(string $username): array
    {
        return Cache::remember(
            "leetcode:skills:{$username}",
            now()->addMinutes(30),
            function () use ($username) {
                $baseUrl = config('services.alfa_leetcode.base_url');

                $response = Http::timeout(15)
                    ->get("{$baseUrl}/{$username}/skill");

                $response->throw();

                return $response->json();
            }
        );
    }

    public function getSubmissions(
        string $username,
        int $limit = 100
    ): array {
        return Cache::remember(
            "leetcode:submissions:{$username}:{$limit}",
            now()->addMinutes(30),
            function () use ($username, $limit) {
                $baseUrl = config('services.alfa_leetcode.base_url');

                $response = Http::timeout(15)
                    ->get("{$baseUrl}/{$username}/submission", [
                        'limit' => $limit,
                    ]);

                $response->throw();

                return $response->json();
            }
        );
    }
}

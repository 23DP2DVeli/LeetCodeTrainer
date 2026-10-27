<?php

namespace App\Services;

use App\Models\Problem;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ProblemCatalogService
{
    public function getProblem(string $slug): array
    {
        return Cache::remember(
            "leetcode:problem:{$slug}",
            now()->addHours(24),
            function () use ($slug) {
                $baseUrl = config('services.alfa_leetcode.base_url');

                $response = Http::timeout(15)
                    ->get("{$baseUrl}/select", [
                        'titleSlug' => $slug,
                    ]);

                $response->throw();

                return $response->json();
            }
        );
    }

    public function syncProblem(string $slug): Problem
    {
        $data = $this->getProblem($slug);

        $title = $data['questionTitle'] ?? null;
        $difficulty = $data['difficulty'] ?? null;
        $description = $data['question'] ?? null;
        $constraints = $data['constraints'] ?? null;
        $hints = $data['hints'] ?? null;

        if (
            ! $title ||
            ! in_array($difficulty, ['Easy', 'Medium', 'Hard'], true)
        ) {
            throw new \UnexpectedValueException(
                "Invalid problem metadata for slug: {$slug}"
            );
        }

        return Problem::updateOrCreate(
            ['slug' => $slug],
            [
                'title' => $title,
                'difficulty' => $difficulty,
                'description' => $description,
                'constraints' => $constraints,
                'hints' => $hints,
                'metadata' => [
                    'leetcode_id' => $data['questionFrontendId'] ?? null,
                    'url' => $data['link'] ?? null,
                    'paid_only' => $data['isPaidOnly'] ?? null,
                    'topic_tags' => $data['topicTags'] ?? [],
                ],
            ]
        );
    }
}

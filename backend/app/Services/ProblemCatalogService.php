<?php

namespace App\Services;


use App\Models\Submission;
use App\Models\Problem;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use App\Models\Topic;

class ProblemCatalogService
{
    public function getCatalogPage(int $limit = 100, int $skip = 0): array
{
    $baseUrl = config('services.alfa_leetcode.base_url');

    $response = Http::timeout(30)
        ->get("{$baseUrl}/problems", [
            'limit' => $limit,
            'skip' => $skip,
        ]);

    $response->throw();

    $data = $response->json();

    if (!isset($data['problemsetQuestionList'])) {
        throw new \UnexpectedValueException(
            'Invalid problem catalog response.'
        );
    }

    return $data;
}
public function syncCatalogItem(array $data): Problem
{
    $slug = $data['titleSlug'] ?? null;
    $title = $data['title'] ?? null;
    $difficulty = $data['difficulty'] ?? null;

    if (
        !$slug ||
        !$title ||
        !in_array($difficulty, ['Easy', 'Medium', 'Hard'], true)
    ) {
        throw new \UnexpectedValueException(
            'Invalid problem catalog item.'
        );
    }

    $problem = Problem::query()->updateOrCreate(
        ['slug' => $slug],
        [
            'title' => $title,
            'difficulty' => $difficulty,
            'acceptance_rate' => $data['acRate'] ?? null,
            'metadata' => [
                'leetcode_id' => $data['questionFrontendId'] ?? null,
                'paid_only' => $data['isPaidOnly'] ?? false,
                'topic_tags' => $data['topicTags'] ?? [],
                'has_solution' => $data['hasSolution'] ?? false,
                'has_video_solution' => $data['hasVideoSolution'] ?? false,
            ],
        ]
    );

    $this->syncTopicTags(
        $problem,
        $data['topicTags'] ?? []
    );

    return $problem;
}

private function syncTopicTags(Problem $problem, array $topicTags): void
{
    $topics = [];

    foreach ($topicTags as $tag) {
        $name = $tag['name'] ?? null;
        $slug = $tag['slug'] ?? null;

        if (!$name || !$slug) {
            continue;
        }

        $topic = Topic::query()->firstOrCreate(
            ['slug' => $slug],
            ['name' => $name]
        );

        $topics[$topic->id] = [
            'relevance_weight' => 1.00,
        ];
    }

    $problem->topics()->sync($topics);
}
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

    $problem = Problem::updateOrCreate(
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
    $this->syncTopicTags(
    $problem,
    $data['topicTags'] ?? []
);

    Submission::query()
        ->where('problem_slug', $slug)
        ->whereNull('problem_id')
        ->update(['problem_id' => $problem->id]);

    return $problem;
}

}

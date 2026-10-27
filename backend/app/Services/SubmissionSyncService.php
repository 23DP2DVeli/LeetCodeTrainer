<?php

namespace App\Services;

use App\Models\Problem;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SubmissionSyncService
{
    public function __construct(
        private LeetCodeService $leetCodeService
    ) {}

    public function sync(User $user, int $limit = 20): array
    {
        if (! $user->leetcode_username) {
            throw new \InvalidArgumentException(
                'The user does not have a LeetCode username.'
            );
        }

        $data = $this->leetCodeService->getSubmissions(
            $user->leetcode_username,
            $limit
        );

        $created = 0;
        $skipped = 0;

        DB::transaction(function () use (
            $data,
            $user,
            &$created,
            &$skipped
        ) {
            foreach ($data['submission'] ?? [] as $item) {
                $slug = $item['titleSlug'] ?? null;
                $timestamp = $item['timestamp'] ?? null;
                $status = $item['statusDisplay'] ?? null;
                $language = $item['lang'] ?? null;

                if (
                    ! $slug ||
                    ! is_numeric($timestamp) ||
                    ! $status ||
                    ! $language
                ) {
                    $skipped++;
                    continue;
                }

                $externalKey = hash('sha256', implode('|', [
                    $slug,
                    $timestamp,
                    $status,
                    $language,
                ]));

                $
$problem = Problem::where('slug', $slug)->first();

$submission = Submission::firstOrCreate(
    [
        'external_key' => $externalKey,
    ],
    [
        'user_id' => $user->id,
        'problem_id' => $problem?->id,
        'problem_slug' => $slug,
        'status' => $status,
        'language' => $language,
        'submitted_at' => now()->setTimestamp((int) $timestamp),
        'source' => 'leetcode_api',
    ]
);


                if ($submission->wasRecentlyCreated) {
                    $created++;
                } else {
                    $skipped++;
                }
            }
        });

        return [
            'received' => count($data['submission'] ?? []),
            'created' => $created,
            'skipped' => $skipped,
        ];
    }
}

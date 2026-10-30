<?php

namespace App\Console\Commands;

use App\Models\Submission;
use App\Services\ProblemCatalogService;
use Illuminate\Console\Command;
use Throwable;

class SyncLeetCodeProblems extends Command
{
    protected $signature = 'leetcode:sync-problems
                            {--limit=20 : Maximum number of unique problems to sync}';

    protected $description = 'Sync problem metadata for submitted LeetCode problems';

    public function handle(ProblemCatalogService $catalogService): int
    {
        $limit = (int) $this->option('limit');

        if ($limit < 1 || $limit > 100) {
            $this->error('The limit must be between 1 and 100.');

            return self::FAILURE;
        }

        $slugs = Submission::query()
            ->where('source', 'leetcode_api')
            ->whereNotNull('problem_slug')
            ->distinct()
            ->orderBy('problem_slug')
            ->limit($limit)
            ->pluck('problem_slug');

        if ($slugs->isEmpty()) {
            $this->info('No problem slugs found in submissions.');

            return self::SUCCESS;
        }

        $created = 0;
        $updated = 0;
        $failed = 0;

        foreach ($slugs as $slug) {
            $this->line("Syncing: {$slug}");

            try {
                $existing = \App\Models\Problem::where(
                    'slug',
                    $slug
                )->exists();

                $problem = $catalogService->syncProblem($slug);

                $this->info(
                    "Synced: {$problem->title} (ID: {$problem->id})"
                );

                if ($existing) {
                    $updated++;
                } else {
                    $created++;
                }
            } catch (Throwable $exception) {
                report($exception);

                $this->error(
                    "Failed to sync {$slug}: {$exception->getMessage()}"
                );

                $failed++;
            }
        }

        $this->newLine();

        $this->table(
            ['Processed', 'Created', 'Updated', 'Failed'],
            [[
                $slugs->count(),
                $created,
                $updated,
                $failed,
            ]]
        );

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}

<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\SubmissionSyncService;
use Illuminate\Console\Command;
use Throwable;

class SyncLeetCodeSubmissions extends Command
{
    protected $signature = 'leetcode:sync-submissions
                            {username : LeetCode username}
                            {--limit=20 : Number of recent submissions to fetch}';

    protected $description = 'Sync recent LeetCode submissions for a user';

    public function handle(SubmissionSyncService $syncService): int
    {
        $username = $this->argument('username');
        $limit = (int) $this->option('limit');

        if ($limit < 1 || $limit > 100) {
            $this->error('The limit must be between 1 and 100.');

            return self::FAILURE;
        }

        $user = User::where('leetcode_username', $username)->first();

        if (! $user) {
            $this->error(
                "No user found with LeetCode username: {$username}"
            );

            return self::FAILURE;
        }

        $this->info("Syncing submissions for {$username}...");

        try {
            $result = $syncService->sync($user, $limit);
        } catch (Throwable $exception) {
            report($exception);

            $this->error('Sync failed: ' . $exception->getMessage());

            return self::FAILURE;
        }

        $this->info('Sync completed.');
        $this->table(
            ['Received', 'Created', 'Skipped'],
            [[
                $result['received'],
                $result['created'],
                $result['skipped'],
            ]]
        );

        return self::SUCCESS;
    }
}

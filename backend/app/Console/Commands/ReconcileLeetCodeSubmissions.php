<?php

namespace App\Console\Commands;

use App\Models\Submission;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ReconcileLeetCodeSubmissions extends Command
{
    protected $signature = 'leetcode:reconcile-submissions
                            {--apply : Apply the reconciliation}';

    protected $description = 'Reconcile duplicate LeetCode submissions';

    public function handle(): int
    {
        $oldRecords = Submission::query()
            ->where('source', 'leetcode_api')
            ->whereNull('problem_slug')
            ->orderBy('id')
            ->get();

        $pairs = [];
        $ambiguous = 0;

        foreach ($oldRecords as $old) {
            $matches = Submission::query()
                ->where('source', 'leetcode_api')
                ->where('user_id', $old->user_id)
                ->where('submitted_at', $old->submitted_at)
                ->where('status', $old->status)
                ->where('language', $old->language)
                ->whereNotNull('problem_slug')
                ->get();

            if ($matches->count() !== 1) {
                $ambiguous++;
                continue;
            }

            $new = $matches->first();

            $pairs[] = [
                'old' => $old,
                'new' => $new,
            ];
        }

        $this->info('Pairs eligible for reconciliation: ' . count($pairs));
        $this->info('Ambiguous or unmatched records: ' . $ambiguous);

        $this->table(
            ['Old ID', 'New ID', 'Problem slug', 'Problem ID'],
            array_map(
                fn (array $pair) => [
                    $pair['old']->id,
                    $pair['new']->id,
                    $pair['new']->problem_slug,
                    $pair['new']->problem_id,
                ],
                $pairs
            )
        );

        if (! $this->option('apply')) {
            $this->warn('Preview only. No records were changed.');
            $this->line('Run with --apply only after reviewing the pairs.');

            return self::SUCCESS;
        }

        DB::transaction(function () use ($pairs) {
            foreach ($pairs as $pair) {
                $old = Submission::query()
                    ->lockForUpdate()
                    ->findOrFail($pair['old']->id);

                $new = Submission::query()
                    ->lockForUpdate()
                    ->findOrFail($pair['new']->id);

                if (
                    $old->problem_slug !== null ||
                    $new->problem_slug === null ||
                    $old->user_id !== $new->user_id ||
                    $old->submitted_at?->equalTo($new->submitted_at) !== true ||
                    $old->status !== $new->status ||
                    $old->language !== $new->language
                ) {
                    throw new \RuntimeException(
                        "Submission pair changed: {$old->id}, {$new->id}"
                    );
                }

                $old->problem_slug = $new->problem_slug;
                $old->problem_id = $new->problem_id ?? $old->problem_id;
                $old->save();

                $new->delete();
            }
        });

        $this->info('Reconciliation completed.');

        return self::SUCCESS;
    }
}

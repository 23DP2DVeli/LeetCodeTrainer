<?php

namespace App\Console\Commands;

use App\Models\Problem;
use App\Models\Submission;
use App\Models\Topic;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class ImportLeetCodeCatalog extends Command
{
    protected $signature = 'leetcode:import-catalog
                            {--dry-run : Preview changes without writing to the database}
                            {--limit=0 : Maximum number of records to process (0 = all)}';

    protected $description = 'Import the LeetCode problem catalogue from the local JSON dataset';

    public function handle(): int
    {
        $path = database_path('data/leetcode_questions.json');
        $limit = (int) $this->option('limit');
        $dryRun = (bool) $this->option('dry-run');

        if ($limit < 0) {
            $this->error('The limit cannot be negative.');

            return self::FAILURE;
        }

        if (! is_file($path)) {
            $this->error("Dataset not found: {$path}");

            return self::FAILURE;
        }

        $this->info('Reading LeetCode dataset...');

        try {
            $records = json_decode(
                file_get_contents($path),
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (Throwable $exception) {
            $this->error('Could not decode JSON: '.$exception->getMessage());

            return self::FAILURE;
        }

        if (! is_array($records)) {
            $this->error('Dataset root must be a JSON array.');

            return self::FAILURE;
        }

        if ($limit > 0) {
            $records = array_slice($records, 0, $limit);
        }

        $this->info('Records to inspect: '.count($records));

        // Build a lookup once instead of querying the database for every record.
        $slugs = [];

        foreach ($records as $record) {
            $question = $record['data']['question'] ?? null;

            if (! is_array($question)) {
                continue;
            }

            $slug = $this->extractSlug($question['url'] ?? null);

            if ($slug !== null) {
                $slugs[] = $slug;
            }
        }

        $existingSlugs = Problem::query()
            ->whereIn('slug', array_unique($slugs))
            ->pluck('slug')
            ->flip()
            ->all();

        $created = 0;
        $updated = 0;
        $skipped = 0;
        $failed = 0;
        $linkedSubmissions = 0;

        $seenSlugs = [];
        $topicCache = [];

        $bar = $this->output->createProgressBar(count($records));
        $bar->start();

        foreach ($records as $record) {
            try {
                $question = $record['data']['question'] ?? null;

                if (! is_array($question)) {
                    $skipped++;
                    $bar->advance();
                    continue;
                }

                $slug = $this->extractSlug($question['url'] ?? null);
                $title = $question['title'] ?? null;
                $difficulty = $question['difficulty'] ?? null;

                if (
                    ! $slug ||
                    ! is_string($title) ||
                    trim($title) === '' ||
                    ! in_array($difficulty, ['Easy', 'Medium', 'Hard'], true)
                ) {
                    $skipped++;
                    $bar->advance();
                    continue;
                }

                // Avoid importing the same slug twice from one dataset.
                if (isset($seenSlugs[$slug])) {
                    $skipped++;
                    $bar->advance();
                    continue;
                }

                $seenSlugs[$slug] = true;

                $wasExisting = isset($existingSlugs[$slug]);

                if ($dryRun) {
                    if ($wasExisting) {
                        $updated++;
                    } else {
                        $created++;
                    }

                    $bar->advance();
                    continue;
                }

                DB::transaction(function () use (
                    $question,
                    $slug,
                    $title,
                    $difficulty,
                    &$topicCache,
                    &$linkedSubmissions
                ) {
                    $problem = Problem::query()->firstOrNew([
                        'slug' => $slug,
                    ]);

                    $metadata = $problem->metadata ?? [];

                    $metadata = array_merge($metadata, [
                        'catalog_source' => 'noworneverev/leetcode-api',
                        'leetcode_question_id' => $question['questionId'] ?? null,
                        'leetcode_frontend_id' => $question['questionFrontendId'] ?? null,
                        'likes' => $question['likes'] ?? null,
                        'dislikes' => $question['dislikes'] ?? null,
                        'category_title' => $question['categoryTitle'] ?? null,
                        'paid_only' => $question['isPaidOnly'] ?? false,
                        'has_solution' => $question['hasSolution'] ?? false,
                        'has_video_solution' => $question['hasVideoSolution'] ?? false,
                        'similar_questions' => $this->decodeJsonField(
                            $question['similarQuestions'] ?? null
                        ),
                        'url' => $question['url'] ?? null,
                    ]);

                    $problem->title = $title;
                    $problem->difficulty = $difficulty;

                    $acceptanceRate = $this->extractAcceptanceRate(
                        $question['stats'] ?? null
                    );

                    if ($acceptanceRate !== null) {
                        $problem->acceptance_rate = $acceptanceRate;
                    }

                    if (! empty($question['content'])) {
                        $problem->description = $question['content'];
                    }

                    if (isset($question['hints']) && is_array($question['hints'])) {
                        $problem->hints = $question['hints'];
                    }

                    $problem->metadata = $metadata;
                    $problem->save();

                    $topicIds = [];

                    foreach (($question['topicTags'] ?? []) as $tag) {
                        $name = $tag['name'] ?? null;

                        if (! is_string($name) || trim($name) === '') {
                            continue;
                        }

                        $name = trim($name);

                        if (! isset($topicCache[$name])) {
                            $baseSlug = Str::slug($name);

                            if ($baseSlug === '') {
                                $baseSlug = 'topic-'.substr(sha1($name), 0, 8);
                            }

                            $topic = Topic::query()
                                ->where('slug', $baseSlug)
                                ->first();

                            // Avoid collisions such as two names producing the same slug.
                            if ($topic && $topic->name !== $name) {
                                $baseSlug .= '-'.substr(sha1($name), 0, 8);
                            }

                            $topicCache[$name] = Topic::query()->firstOrCreate(
                                ['slug' => $baseSlug],
                                ['name' => $name]
                            )->id;
                        }

                        $topicIds[] = $topicCache[$name];
                    }

                    if ($topicIds !== []) {
                        $problem->topics()->syncWithoutDetaching(
                            array_fill_keys(array_unique($topicIds), [
                                'relevance_weight' => 1.00,
                            ])
                        );
                    }

                    // Attach previously imported submissions that have no problem_id yet.
                    $linkedSubmissions += Submission::query()
                        ->where('problem_slug', $slug)
                        ->whereNull('problem_id')
                        ->update(['problem_id' => $problem->id]);
                });

                if ($wasExisting) {
                    $updated++;
                } else {
                    $created++;
                }
            } catch (Throwable $exception) {
                $failed++;

                report($exception);

                if ($failed <= 10) {
                    $this->newLine();
                    $this->warn(
                        'Import error: '.$exception->getMessage()
                    );
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->table(
            ['Mode', 'Created', 'Updated', 'Skipped', 'Failed', 'Submissions linked'],
            [[
                $dryRun ? 'DRY RUN' : 'IMPORT',
                $created,
                $updated,
                $skipped,
                $failed,
                $dryRun ? 'Not checked' : $linkedSubmissions,
            ]]
        );

        if ($dryRun) {
            $this->comment('Dry run completed. No database records were changed.');
        }

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function extractSlug(mixed $url): ?string
    {
        if (! is_string($url) || trim($url) === '') {
            return null;
        }

        $path = parse_url($url, PHP_URL_PATH);

        if (! is_string($path)) {
            return null;
        }

        $segments = array_values(array_filter(explode('/', trim($path, '/'))));

        $problemsIndex = array_search('problems', $segments, true);

        if ($problemsIndex === false || ! isset($segments[$problemsIndex + 1])) {
            return null;
        }

        return $segments[$problemsIndex + 1];
    }

    private function extractAcceptanceRate(mixed $stats): ?float
    {
        if (is_string($stats)) {
            $stats = $this->decodeJsonField($stats);
        }

        if (! is_array($stats)) {
            return null;
        }

        $rate = $stats['acRate'] ?? null;

        if (! is_string($rate) && ! is_numeric($rate)) {
            return null;
        }

        $rate = (float) str_replace('%', '', (string) $rate);

        if (! is_finite($rate) || $rate < 0 || $rate > 100) {
            return null;
        }

        return $rate;
    }

    private function decodeJsonField(mixed $value): mixed
    {
        if (! is_string($value)) {
            return $value;
        }

        $decoded = json_decode($value, true);

        return json_last_error() === JSON_ERROR_NONE ? $decoded : null;
    }
}

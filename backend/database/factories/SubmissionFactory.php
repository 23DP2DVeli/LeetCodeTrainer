<?php

namespace Database\Factories;

use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Submission>
 */
class SubmissionFactory extends Factory
{
    protected $model = Submission::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'problem_id' => null,
            'problem_slug' => fake()->slug(),
            'status' => 'Accepted',
            'language' => 'javascript',
            'submitted_at' => now(),
            'source' => 'leetcode_api',
            'external_key' => hash('sha256', fake()->uuid()),
        ];
    }
}

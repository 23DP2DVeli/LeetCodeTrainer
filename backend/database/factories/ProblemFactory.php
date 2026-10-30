<?php

namespace Database\Factories;

use App\Models\Problem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Problem>
 */
class ProblemFactory extends Factory
{
    protected $model = Problem::class;

    public function definition(): array
    {
        return [
            'slug' => fake()->unique()->slug(),
            'title' => fake()->sentence(3),
            'difficulty' => fake()->randomElement([
                'Easy',
                'Medium',
                'Hard',
            ]),
            'description' => fake()->paragraph(),
            'constraints' => null,
            'hints' => null,
            'metadata' => [],
        ];
    }
}

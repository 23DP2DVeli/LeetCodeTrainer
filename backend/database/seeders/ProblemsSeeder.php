<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Problem;
use App\Models\Topic;



class ProblemsSeeder extends Seeder {
    public function run(): void
    {
        $find= ['slug' => 'two-sum'];
        $update = ['title' => 'Two Sum', 'difficulty' => 'Easy', 'acceptance_rate' => null];

        $problem = Problem::updateOrCreate($find, $update);

        $topicIds = Topic::whereIn('slug', ['array', 'hash-table'])->get()->pluck('id')->all();
        $problem->topics()->syncWithoutDetaching($topicIds);

    }
}

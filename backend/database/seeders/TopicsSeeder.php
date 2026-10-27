<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TopicsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $topics = [
            ['name' => 'Array', 'slug' => 'array', 'description' => 'Problems involving array data structures and operations'],
            ['name' => 'String', 'slug' => 'string', 'description' => 'String manipulation, pattern matching, and text processing'],
            ['name' => 'Hash Table', 'slug' => 'hash-table', 'description' => 'Hash maps, dictionaries, and associative arrays'],
            ['name' => 'Dynamic Programming', 'slug' => 'dynamic-programming', 'description' => 'Optimization problems using memoization and tabulation'],
            ['name' => 'Math', 'slug' => 'math', 'description' => 'Mathematical concepts, number theory, and calculations'],
            ['name' => 'Sorting', 'slug' => 'sorting', 'description' => 'Sorting algorithms and related techniques'],
            ['name' => 'Greedy', 'slug' => 'greedy', 'description' => 'Greedy algorithms making locally optimal choices'],
            ['name' => 'Depth-First Search', 'slug' => 'depth-first-search', 'description' => 'DFS traversal on graphs and trees'],
            ['name' => 'Binary Search', 'slug' => 'binary-search', 'description' => 'Binary search and divide-and-conquer techniques'],
            ['name' => 'Database', 'slug' => 'database', 'description' => 'SQL queries and database operations'],
            ['name' => 'Breadth-First Search', 'slug' => 'breadth-first-search', 'description' => 'BFS traversal on graphs and trees'],
            ['name' => 'Tree', 'slug' => 'tree', 'description' => 'Tree data structures and algorithms'],
            ['name' => 'Matrix', 'slug' => 'matrix', 'description' => '2D array and matrix operations'],
            ['name' => 'Two Pointers', 'slug' => 'two-pointers', 'description' => 'Two-pointer technique for array/string problems'],
            ['name' => 'Bit Manipulation', 'slug' => 'bit-manipulation', 'description' => 'Bitwise operations and binary representation'],
            ['name' => 'Stack', 'slug' => 'stack', 'description' => 'Stack data structure and LIFO operations'],
        ];

        foreach ($topics as $topic) {
            $update = $topic;
            \App\Models\Topic::updateOrCreate(['slug' => $topic['slug']], $update);
        }
    }
}

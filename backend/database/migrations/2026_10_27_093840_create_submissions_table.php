<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('problem_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('status');
            $table->string('language')->nullable();
            $table->timestamp('submitted_at')->nullable();

            $table->string('source')->default('leetcode_api');

            $table->timestamps();

            $table->index(['user_id', 'submitted_at']);
            $table->index(['problem_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};

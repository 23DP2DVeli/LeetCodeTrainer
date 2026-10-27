<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('problem_topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('problem_id')->constrained()->cascadeOnDelete();
            $table->foreignId('topic_id')->constrained()->cascadeOnDelete();
            $table->decimal('relevance_weight', 3, 2)->default(1.00);
            $table->timestamps();

            $table->unique(['problem_id', 'topic_id']);
            $table->index('topic_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('problem_topics');
    }
};

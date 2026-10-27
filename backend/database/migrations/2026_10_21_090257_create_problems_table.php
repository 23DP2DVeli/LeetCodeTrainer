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
        Schema::create('problems', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->enum('difficulty', ['Easy', 'Medium', 'Hard']);
            $table->decimal('acceptance_rate', 5, 2)->nullable();
            $table->text('description')->nullable();
            $table->text('constraints')->nullable();
            $table->json('hints')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('difficulty');
            $table->index('acceptance_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('problems');
    }
};

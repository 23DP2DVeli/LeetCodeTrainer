<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('problems', function (Blueprint $table) {
            $table->decimal('irt_difficulty', 6, 3)
                ->default(0.000)
                ->after('difficulty');

            $table->decimal('irt_discrimination', 5, 3)
                ->default(1.000)
                ->after('irt_difficulty');
        });
    }

    public function down(): void
    {
        Schema::table('problems', function (Blueprint $table) {
            $table->dropColumn([
                'irt_difficulty',
                'irt_discrimination',
            ]);
        });
    }
};

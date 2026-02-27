<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migration: direction can link to multiple ingredients.
     * Each row stores the "step amount" (serving_id) for that ingredient in this direction.
     */
    public function up(): void
    {
        Schema::create('direction_ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('direction_id')->constrained('directions', 'id')->cascadeOnDelete();
            $table->foreignId('ingredient_id')->constrained('ingredients', 'id');
            $table->foreignId('serving_id')->constrained('servings', 'id');
            $table->dateTime('created_at')->default(Carbon::now());
            $table->dateTime('updated_at')->default(Carbon::now());
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('direction_ingredients');
    }
};

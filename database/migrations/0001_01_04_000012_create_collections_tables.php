<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('collections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users', 'id')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->enum('type', ['bag', 'menu'])->default('bag');
            $table->boolean('is_public')->default(false);
            $table->dateTime('created_at')->default(Carbon::now());
            $table->dateTime('updated_at')->default(Carbon::now());

            $table->unique(['user_id', 'slug']);
        });

        Schema::create('collection_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collection_id')->constrained('collections', 'id')->cascadeOnDelete();
            $table->foreignId('recipe_id')->constrained('recipes', 'id')->cascadeOnDelete();
            $table->unsignedInteger('position')->default(0);
            $table->date('scheduled_date')->nullable();
            $table->enum('meal_slot', ['breakfast', 'lunch', 'dinner', 'snack'])->nullable();
            $table->text('note')->nullable();
            $table->dateTime('created_at')->default(Carbon::now());
            $table->dateTime('updated_at')->default(Carbon::now());

            $table->unique(['collection_id', 'recipe_id', 'scheduled_date', 'meal_slot'], 'collection_item_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collection_items');
        Schema::dropIfExists('collections');
    }
};

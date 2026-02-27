<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('user_recipe_activity', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users', 'id')->cascadeOnDelete();
            $table->foreignId('recipe_id')->constrained('recipes', 'id')->cascadeOnDelete();
            $table->enum('action', ['viewed', 'cooked', 'saved', 'shared'])->index();
            $table->dateTime('created_at')->default(Carbon::now());

            $table->index(['user_id', 'action', 'created_at']);
            $table->index(['user_id', 'recipe_id', 'action']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_recipe_activity');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('recipe_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')->constrained('recipes', 'id')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users', 'id')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('recipe_comments', 'id')->cascadeOnDelete();
            $table->text('body');
            $table->dateTime('created_at')->default(Carbon::now());
            $table->dateTime('updated_at')->default(Carbon::now());
            $table->softDeletes();

            $table->index(['recipe_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipe_comments');
    }
};

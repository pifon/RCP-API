<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('recipe_sensations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')->constrained('recipes', 'id')->cascadeOnDelete();
            $table->foreignId('sensation_id')->constrained('sensations', 'id')->cascadeOnDelete();
            $table->unsignedTinyInteger('intensity')->default(0);
            $table->dateTime('created_at')->default(Carbon::now());
            $table->dateTime('updated_at')->default(Carbon::now());

            $table->unique(['recipe_id', 'sensation_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipe_sensations');
    }
};

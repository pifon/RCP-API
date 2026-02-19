<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recipe_taste_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')->unique()->constrained('recipes', 'id')->cascadeOnDelete();
            $table->unsignedTinyInteger('sweet')->default(0);
            $table->unsignedTinyInteger('sour')->default(0);
            $table->unsignedTinyInteger('salty')->default(0);
            $table->unsignedTinyInteger('bitter')->default(0);
            $table->unsignedTinyInteger('umami')->default(0);
            $table->dateTime('created_at')->default(Carbon::now());
            $table->dateTime('updated_at')->default(Carbon::now());
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipe_taste_profiles');
    }
};

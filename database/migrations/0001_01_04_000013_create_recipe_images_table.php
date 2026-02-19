<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recipe_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')->constrained('recipes', 'id')->cascadeOnDelete();
            $table->string('path');
            $table->enum('type', ['cover', 'step', 'gallery'])->default('gallery');
            $table->unsignedSmallInteger('position')->default(0);
            $table->string('alt_text')->nullable();
            $table->dateTime('created_at')->default(Carbon::now());
            $table->dateTime('updated_at')->default(Carbon::now());
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipe_images');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->dateTime('created_at')->default(Carbon::now());
            $table->dateTime('updated_at')->default(Carbon::now());
        });

        Schema::create('recipe_equipment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')->constrained('recipes', 'id')->cascadeOnDelete();
            $table->foreignId('equipment_id')->constrained('equipment', 'id')->cascadeOnDelete();
            $table->boolean('optional')->default(false);
            $table->text('note')->nullable();

            $table->unique(['recipe_id', 'equipment_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipe_equipment');
        Schema::dropIfExists('equipment');
    }
};

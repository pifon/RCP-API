<?php

use Carbon\Carbon;
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
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique('slug');
            $table->foreignId('variant_id')->nullable()->constrained('recipes', 'id');
            $table->foreignId('author_id')->constrained('authors');
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('cuisine_id')->constrained('cuisines');
            $table->foreignId('dish_type_id')->constrained('dish_types');
            $table->dateTime('created_at')->default(Carbon::now());
            $table->dateTime('updated_at')->default(Carbon::now());
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users', 'id')->cascadeOnDelete();
            $table->unsignedTinyInteger('spice_tolerance')->default(50);
            $table->dateTime('created_at')->default(Carbon::now());
            $table->dateTime('updated_at')->default(Carbon::now());
        });

        Schema::create('user_preferred_cuisines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users', 'id')->cascadeOnDelete();
            $table->foreignId('cuisine_id')->constrained('cuisines', 'id')->cascadeOnDelete();

            $table->unique(['user_id', 'cuisine_id']);
        });

        Schema::create('user_excluded_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users', 'id')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products', 'id')->cascadeOnDelete();

            $table->unique(['user_id', 'product_id']);
        });

        Schema::create('user_dietary_restrictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users', 'id')->cascadeOnDelete();
            $table->foreignId('allergen_id')->constrained('allergens', 'id')->cascadeOnDelete();

            $table->unique(['user_id', 'allergen_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_dietary_restrictions');
        Schema::dropIfExists('user_excluded_products');
        Schema::dropIfExists('user_preferred_cuisines');
        Schema::dropIfExists('user_preferences');
    }
};

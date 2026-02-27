<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('shopping_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users', 'id')->cascadeOnDelete();
            $table->foreignId('collection_id')->nullable()->constrained('collections', 'id')->nullOnDelete();
            $table->string('name');
            $table->enum('status', ['active', 'completed', 'archived'])->default('active')->index();
            $table->dateTime('created_at')->default(Carbon::now());
            $table->dateTime('updated_at')->default(Carbon::now());
        });

        Schema::create('shopping_list_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shopping_list_id')->constrained('shopping_lists', 'id')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products', 'id')->cascadeOnDelete();
            $table->decimal('quantity', 10, 3)->default(1);
            $table->foreignId('measure_id')->nullable()->constrained('measures', 'id')->nullOnDelete();
            $table->foreignId('recipe_id')->nullable()->constrained('recipes', 'id')->nullOnDelete();
            $table->boolean('checked')->default(false);
            $table->dateTime('created_at')->default(Carbon::now());
            $table->dateTime('updated_at')->default(Carbon::now());

            $table->index(['shopping_list_id', 'checked']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shopping_list_items');
        Schema::dropIfExists('shopping_lists');
    }
};

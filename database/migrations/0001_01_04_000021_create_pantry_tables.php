<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pantry_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users', 'id')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products', 'id')->cascadeOnDelete();
            $table->decimal('quantity', 10, 3);
            $table->foreignId('measure_id')->nullable()->constrained('measures', 'id')->nullOnDelete();
            $table->date('expires_at')->nullable();
            $table->date('best_before')->nullable();
            $table->date('opened_at')->nullable();
            $table->dateTime('created_at')->default(Carbon::now());
            $table->dateTime('updated_at')->default(Carbon::now());

            $table->index(['user_id', 'product_id']);
            $table->index(['user_id', 'expires_at']);
        });

        Schema::create('pantry_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users', 'id')->cascadeOnDelete();
            $table->foreignId('pantry_item_id')->nullable()->constrained('pantry_items', 'id')->nullOnDelete();
            $table->foreignId('product_id')->constrained('products', 'id')->cascadeOnDelete();
            $table->enum('action', ['added', 'consumed', 'expired', 'adjusted', 'transferred'])->index();
            $table->decimal('quantity_change', 10, 3);
            $table->string('source_type')->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->text('note')->nullable();
            $table->dateTime('created_at')->default(Carbon::now());

            $table->index(['user_id', 'action', 'created_at']);
            $table->index(['source_type', 'source_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pantry_logs');
        Schema::dropIfExists('pantry_items');
    }
};

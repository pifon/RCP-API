<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Map a requested product slug (e.g. misspelling or alternate name) to an existing product.
     * When resolving by slug fails, we look up here to suggest or auto-use the replacement.
     */
    public function up(): void
    {
        Schema::create('product_slug_replacements', function (Blueprint $table) {
            $table->id();
            $table->string('original_slug', 255)->unique();
            $table->unsignedBigInteger('replacement_product_id');
            $table->foreign('replacement_product_id')->references('id')->on('products')->cascadeOnDelete();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent();

            $table->index('original_slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_slug_replacements');
    }
};

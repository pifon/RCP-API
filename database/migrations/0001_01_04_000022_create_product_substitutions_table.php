<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('product_substitutions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products', 'id')->cascadeOnDelete();
            $table->foreignId('substitute_id')->constrained('products', 'id')->cascadeOnDelete();
            $table->decimal('ratio', 5, 3)->default(1.000);
            $table->text('note')->nullable();
            $table->dateTime('created_at')->default(Carbon::now());
            $table->dateTime('updated_at')->default(Carbon::now());

            $table->unique(['product_id', 'substitute_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_substitutions');
    }
};

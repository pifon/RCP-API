<?php

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
        Schema::create('elements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('type')->constrained('element_types');
            $table->foreignId('sub_type')->constrained('element_sub_types');
            $table->string('name');
            $table->string('aka')->nullable();
            $table->json('names')->nullable();
            $table->text('description')->nullable();
            $table->string('ref')->nullable();
            $table->dateTime('created_at')->default(Carbon::now());
            $table->dateTime('updated_at')->default(Carbon::now());
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('elements');
    }
};

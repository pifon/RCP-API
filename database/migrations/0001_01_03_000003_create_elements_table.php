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
        Schema::create('elements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('type_id')->constrained('element_types', 'id');
            $table->foreignId('sub_type_id')->nullable()->constrained('element_sub_types', 'id');
            $table->string('name')->unique('name');
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

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('measure_conversions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_measure_id')->constrained('measures', 'id')->cascadeOnDelete();
            $table->foreignId('to_measure_id')->constrained('measures', 'id')->cascadeOnDelete();
            $table->decimal('factor', 12, 6);
            $table->dateTime('created_at')->default(Carbon::now());
            $table->dateTime('updated_at')->default(Carbon::now());

            $table->unique(['from_measure_id', 'to_measure_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('measure_conversions');
    }
};

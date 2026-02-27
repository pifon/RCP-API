<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedSmallInteger('shelf_life_days')->nullable()->after('fiber_per_100g');
            $table->unsignedSmallInteger('shelf_life_opened_days')->nullable()->after('shelf_life_days');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['shelf_life_days', 'shelf_life_opened_days']);
        });
    }
};

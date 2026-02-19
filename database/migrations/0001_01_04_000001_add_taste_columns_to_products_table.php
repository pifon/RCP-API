<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedTinyInteger('taste_sweet')->default(0)->after('allergen');
            $table->unsignedTinyInteger('taste_sour')->default(0)->after('taste_sweet');
            $table->unsignedTinyInteger('taste_salty')->default(0)->after('taste_sour');
            $table->unsignedTinyInteger('taste_bitter')->default(0)->after('taste_salty');
            $table->unsignedTinyInteger('taste_umami')->default(0)->after('taste_bitter');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['taste_sweet', 'taste_sour', 'taste_salty', 'taste_bitter', 'taste_umami']);
        });
    }
};

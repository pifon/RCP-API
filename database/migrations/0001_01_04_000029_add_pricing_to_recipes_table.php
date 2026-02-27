<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('recipes', function (Blueprint $table) {
            $table->decimal('price', 8, 2)->nullable()->after('source_description');
            $table->char('currency', 3)->nullable()->after('price');
            $table->unsignedTinyInteger('fork_revenue_percent')->default(0)->after('currency');
        });
    }

    public function down(): void
    {
        Schema::table('recipes', function (Blueprint $table) {
            $table->dropColumn(['price', 'currency', 'fork_revenue_percent']);
        });
    }
};

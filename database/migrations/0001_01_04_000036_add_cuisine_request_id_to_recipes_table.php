<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recipes', function (Blueprint $table) {
            $table->unsignedBigInteger('cuisine_request_id')
                ->nullable()
                ->after('cuisine_id');

            $table->foreign('cuisine_request_id')
                ->references('id')
                ->on('cuisine_requests')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('recipes', function (Blueprint $table) {
            $table->dropForeign(['cuisine_request_id']);
            $table->dropColumn('cuisine_request_id');
        });
    }
};

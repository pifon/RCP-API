<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recipes', function (Blueprint $table) {
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft')->after('description');
            $table->unsignedSmallInteger('prep_time_minutes')->nullable()->after('status');
            $table->unsignedSmallInteger('cook_time_minutes')->nullable()->after('prep_time_minutes');
            $table->enum('difficulty', ['easy', 'medium', 'hard', 'expert'])->nullable()->after('cook_time_minutes');
            $table->unsignedTinyInteger('serves')->nullable()->after('difficulty');
            $table->foreignId('forked_from_id')->nullable()->after('variant_id')
                ->constrained('recipes', 'id')->nullOnDelete();
            $table->dateTime('published_at')->nullable()->after('serves');
        });
    }

    public function down(): void
    {
        Schema::table('recipes', function (Blueprint $table) {
            $table->dropForeign(['forked_from_id']);
            $table->dropColumn([
                'status', 'prep_time_minutes', 'cook_time_minutes',
                'difficulty', 'serves', 'forked_from_id', 'published_at',
            ]);
        });
    }
};

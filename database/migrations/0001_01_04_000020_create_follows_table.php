<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('follows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('follower_id')->constrained('users', 'id')->cascadeOnDelete();
            $table->string('followable_type')->index();
            $table->unsignedBigInteger('followable_id');
            $table->dateTime('created_at')->default(Carbon::now());

            $table->unique(['follower_id', 'followable_type', 'followable_id']);
            $table->index(['followable_type', 'followable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('follows');
    }
};

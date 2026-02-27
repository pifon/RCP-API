<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('cuisine_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 255);
            $table->string('variant', 255)->nullable();
            $table->text('description')->nullable();
            $table->string('status', 20)->default('pending');
            $table->unsignedBigInteger('requested_by');
            $table->unsignedBigInteger('cuisine_id')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();

            $table->foreign('requested_by')->references('id')->on('authors');
            $table->foreign('cuisine_id')->references('id')->on('cuisines')->nullOnDelete();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuisine_requests');
    }
};

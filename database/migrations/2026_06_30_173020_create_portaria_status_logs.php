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
        Schema::create('portaria_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portaria_id');
            $table->foreignId('user_id');
            $table->enum('from_status', []);
            $tablee->enum('to_statuss', [])->nullable();
            $table->string('comment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('portaria_status_logs');
    }
};

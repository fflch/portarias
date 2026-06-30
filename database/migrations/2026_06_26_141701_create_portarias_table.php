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
        Schema::create("portarias", function(Blueprint $table) {
            $table->integer('number');
            $table->date('date');
            $table->enum('type', ['cpp', 'comissao', 'designacao', 'administrativa']);
            $table->string('status');
            $table->boolean('is_legacy');
            $table->foreignId('revokes_id')->nullable();
            $table->foreignId('created_by');
            $table->foreignId('approved_by');
            $table->timestamp('published_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

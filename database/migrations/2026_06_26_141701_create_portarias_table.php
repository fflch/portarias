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
            $table->string('type');
            $table->string('status')->default("rascunho");
            $table->boolean('is_legacy')->default(false);
            $table->foreignId('revokes_id')->nullable();
            $table->foreignId('reviewer_id');
            $table->foreignId('created_by');
            $table->foreignId('approved_by')->nullable();
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

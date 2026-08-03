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
        Schema::create('portarias', function (Blueprint $table) {
            $table->id();

            $table->string('type');          
            $table->string('title', 500);

            $table->integer('number')->nullable();
            $table->integer('year');

            $table->date('published_at')->nullable();

            $table->string('file_path')->nullable();
            $table->string('file_name');
            $table->string('file_hash')->nullable();

            $table->string('status')->default('rascunho'); 
            $table->text('rejection_reason')->nullable();

            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['number', 'year', 'type'], 'unique_portaria_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('portarias');
    }
};

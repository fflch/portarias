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
        Schema::create('portaria_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portaria_id');
            $table->string('path');
            $table->enum('disk', ['local', 's3']);
            $table->string('origin_name');
            $table->string('mime_type');
            $table->integer('version');
            $table->boolean('is_current');
            $table->foreignId('uploaded_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('portaria_attachments');
    }
};

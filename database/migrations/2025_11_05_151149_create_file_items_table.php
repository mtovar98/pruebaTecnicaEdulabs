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
        Schema::create('file_items', function (Illuminate\Database\Schema\Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->string('original_name');        // nombre como lo subió el usuario
            $table->string('stored_path');          // path en storage/app/public/...
            $table->unsignedBigInteger('size_bytes');
            $table->string('mime_type', 191)->nullable();
            $table->string('extension', 20)->nullable();

            // opcionales útiles para control/seguridad
            $table->string('checksum', 191)->nullable(); // sha256/md5 del archivo
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_items');
    }
};

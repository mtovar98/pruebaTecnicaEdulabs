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
        Schema::create('roles', function (Illuminate\Database\Schema\Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // 'admin', 'user'
            $table->string('label')->nullable(); // opcional para mostrar bonito: 'Administrador', 'Usuario'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};

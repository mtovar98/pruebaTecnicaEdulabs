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
        Schema::create('banned_extensions', function (Illuminate\Database\Schema\Blueprint $table) {
            $table->id();
            $table->string('extension', 20)->unique(); // ej. exe, bat, js, php, sh
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banned_extensions');
    }
};

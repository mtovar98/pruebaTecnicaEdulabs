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
        Schema::create('storage_global_settings', function (Illuminate\Database\Schema\Blueprint $table) {
            $table->id();
            $table->unsignedInteger('default_quota_mb')->default(10); // cuota global por defecto (MB)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('storage_global_settings');
    }
};

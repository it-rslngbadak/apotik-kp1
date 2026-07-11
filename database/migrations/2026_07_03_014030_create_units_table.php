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
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('slug')->index()->nullable();
            $table->string('kode_pendapatan')->nullable();
            $table->string('desc_pendapatan')->nullable();
            $table->string('kode_biaya')->nullable();
            $table->string('desc_biaya')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.W
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};

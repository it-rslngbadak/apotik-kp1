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
        Schema::create('program_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->references('id')->on('units');
            $table->string('slug')->index()->nullable();
            $table->string('nama_program');
            $table->text('ket_program')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_units');
    }
};

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
        Schema::create('rkaps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_unit_id')->references('id')->on('program_units');
            $table->foreignId('kode_transaksi_id')->references('id')->on('kode_transaksi');
            $table->string('desc_transaksi');
            $table->integer('jumlah');
            $table->string('satuan');
            $table->integer('harga_satuan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rkaps');
    }
};

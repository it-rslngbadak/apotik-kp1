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
        Schema::create('ref_pendapatan_units', function (Blueprint $table) {
            $table->id();
            $table->string('tahun');
            $table->string('unit_id');
            $table->string('nama_unit');
            $table->string('kode_transaksi');
            $table->string('nama_transaksi');
            $table->string('cara_bayar');
            $table->integer('jumlah');
            $table->string('satuan')->nullable();
            $table->integer('total');
            $table->string('coa_pendapatan')->nullable();
            $table->string('coa_biaya')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ref_pendapatan_units');
    }
};

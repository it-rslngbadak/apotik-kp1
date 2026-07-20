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
        Schema::create('kode_transaksi', function (Blueprint $table) {
            $table->id();
            $table->string('kode');
            $table->string('nama_transaksi');
            $table->text('desc');
            $table->enum('jenis_kode', ['Pendapatan', 'Biaya']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kode_transaksi');
    }
};

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
        Schema::create('transaksi_obat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->references('id')->on('customers');
            $table->string('slug');
            $table->string('farmalkes_id');
            $table->string('nama_obat');
            $table->integer('jumlah');
            $table->integer('hna')->nullable();
            $table->integer('harga_jual');
            $table->integer('ppn')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_obat');
    }
};

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
        Schema::create('master_tindakan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_tindakan')->index();
            $table->string('eselon');
            $table->string('tarif_kamar')->nullable();
            $table->string('tarif_rj')->nullable();
            $table->string('tarif_ugd')->nullable();
            $table->string('tarif_kls_3')->nullable();
            $table->string('tarif_kls_2')->nullable();
            $table->string('tarif_kls_1')->nullable();
            $table->string('tarif_kls_vip')->nullable();
            $table->string('tarif_kls_icu')->nullable();
            $table->string('tarif_kls_isolasi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_tindakan');
    }
};

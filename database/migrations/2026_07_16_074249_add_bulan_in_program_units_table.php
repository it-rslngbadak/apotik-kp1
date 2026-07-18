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
        Schema::table('program_units', function (Blueprint $table) {
            $table->string('bulan')->nullable();
            $table->string('kategori')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('program_units', function (Blueprint $table) {
            $table->dropColumn('bulan');
            $table->dropColumn('kategori');
        });
    }
};

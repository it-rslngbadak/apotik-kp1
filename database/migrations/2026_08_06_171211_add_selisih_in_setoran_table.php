<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('setoran', function (Blueprint $table) {
            $table->bigInteger('total_tunai_customer')->nullable()->after('status');
            $table->bigInteger('selisih')->nullable()->after('total_tunai_customer');
        });
    }

    public function down(): void
    {
        Schema::table('setoran', function (Blueprint $table) {
            $table->dropColumn(['total_tunai_customer', 'selisih']);
        });
    }
};

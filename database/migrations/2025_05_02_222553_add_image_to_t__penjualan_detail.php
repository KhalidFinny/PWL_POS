<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('t_penjualan_detail', function (Blueprint $table) {
            $table->string('image')->nullable()->after('jumlah');
        });
    }

    public function down(): void
    {
        Schema::table('t_penjualan_detail', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class oncascade extends Migration
{
    public function up()
    {
        Schema::table('t_stok', function (Blueprint $table) {
            // Drop existing foreign key
            $table->dropForeign(['barang_id']);
            // Add foreign key with ON DELETE CASCADE
            $table->foreign('barang_id')
                  ->references('barang_id')
                  ->on('m_barang')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('t_stok', function (Blueprint $table) {
            // Revert to a basic foreign key without cascade
            $table->dropForeign(['barang_id']);
            $table->foreign('barang_id')
                  ->references('barang_id')
                  ->on('m_barang');
        });
    }
}

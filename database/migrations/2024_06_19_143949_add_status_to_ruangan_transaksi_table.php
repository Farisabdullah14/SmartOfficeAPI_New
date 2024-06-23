<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToRuanganTransaksiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ruangan_transaksi', function (Blueprint $table) {
            $table->string('status')->default('on'); // default status 'on'
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ruangan_transaksi', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}

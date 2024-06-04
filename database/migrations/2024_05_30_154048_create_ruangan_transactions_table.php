<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRuanganTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ruangan_transaksi', function (Blueprint $table) {
            $table->id();
            $table->string('id_ruangan');
            $table->string('id_ruangan_transaksi')->unique(); // Tambahkan kolom id_ruangan_transaksi
            $table->string('status')->default('on');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->foreignId('user_id');
            $table->string('jumlah_orang_yang_hadir');
            $table->string('pin_ruangan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ruangan_transaksi');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePinActivityRuangan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pin_activity_ruangan', function (Blueprint $table) {
            $table->id();
            $table->string('id_pin_activity_ruangan')->unique();
            $table->string('id_ruangan_transaksi');
            $table->String('id_ruangan');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->foreignId('user_id');
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
        Schema::dropIfExists('pin_activity_ruangan');
    }
}

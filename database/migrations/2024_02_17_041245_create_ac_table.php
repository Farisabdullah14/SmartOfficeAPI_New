<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAcTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ac', function (Blueprint $table) {
            $table->id();
            $table->string('id_AC');
            $table->string('jenis_ac');
            $table->integer('watt_ac');
            $table->string('kode_hardware');
            $table->string('id_ruangan');
            $table->string('daya_va');
            $table->string('paard_kracht');
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
        Schema::dropIfExists('ac');
    }
}

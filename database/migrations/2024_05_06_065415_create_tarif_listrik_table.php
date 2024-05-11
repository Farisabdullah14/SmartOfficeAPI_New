<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTarifListrikTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tarif_listrik', function (Blueprint $table) {
            $table->id();
            $table->string('kode_tarif')->unique();
            $table->string('daya');
            $table->decimal('tarif_per_kwh', 10, 2);
            $table->string('golongan');
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
        Schema::dropIfExists('tarif_listrik');
    }
}

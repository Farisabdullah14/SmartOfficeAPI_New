<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKunciRuanganTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kunci_ruangan', function (Blueprint $table) {
            $table->id(); // ini akan menjadi id primary key otomatis
            $table->string('id_key')->unique(); // id_key dengan format khusus
            $table->unsignedBigInteger('id_ruangan');
            $table->string('url_ruangan');
            $table->string('status');
            $table->unsignedBigInteger('id_user');
            $table->timestamps(); // ini akan menambahkan kolom created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kunci_ruangan');
    }
}

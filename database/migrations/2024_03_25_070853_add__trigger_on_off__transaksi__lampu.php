<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddTriggerOnOffTransaksiLampu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('transaksi_lampu', function (Blueprint $table) {
            $table->string('Status')->default('Off');
        });
        // Trigger for setting Status to "On" before insert
        DB::unprepared('
        CREATE TRIGGER set_status_on_before_insert
        BEFORE INSERT ON transaksi_lampu
        FOR EACH ROW
        SET NEW.Status = "On";
        ');

        // Trigger for setting Status to "Off" before update
        DB::unprepared('
        CREATE TRIGGER set_status_off_before_update
        BEFORE UPDATE ON transaksi_lampu
        FOR EACH ROW
        SET NEW.Status = "Off";
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS set_status_on_before_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS set_status_off_before_update');
    }
}
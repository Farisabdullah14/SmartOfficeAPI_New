<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateInsertHistoryTransaksiAcTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
        CREATE TRIGGER insert_id_automatis_history_transaksi_ac BEFORE INSERT ON history_transaksi_ac
        FOR EACH ROW
        BEGIN
            DECLARE next_id INT;
            SET next_id = (SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = "history_transaksi_ac");
            SET NEW.id_history_transaksi_AC = CONCAT("HIST_", LPAD(next_id, 6, "0"));
        END
    
        ');

        DB::unprepared('
            CREATE TRIGGER insert_history_transaksi_ac AFTER INSERT ON transaksi_ac
            FOR EACH ROW
            BEGIN
                INSERT INTO history_transaksi_ac (
                    id_Transaksi_AC,
                    id_ruangan,
                    id_AC,
                    Watt_AC,
                    Kecepatan_kipas,
                    Kode_hardware,
                    Kecepatan_Pendingin,
                    Mode,
                    Temp,
                    Time,
                    Swing,
                    Biaya_AC,
                    Start_waktu,
                    End_waktu,
                    Tarif_Listrik,
                    id_pengguna,
                    Waktu_Penggunaan,
                    Status
                ) VALUES (
                    NEW.id_Transaksi_AC,
                    NEW.id_ruangan,
                    NEW.id_AC,
                    NEW.Watt_AC,
                    NEW.Kecepatan_kipas,
                    NEW.Kode_hardware,
                    NEW.Kecepatan_Pendingin,
                    NEW.Mode,
                    NEW.Temp,
                    NEW.Time,
                    NEW.Swing,
                    NEW.Biaya_AC,
                    NEW.Start_waktu,
                    NEW.End_waktu,
                    NEW.Tarif_Listrik,
                    NEW.id_pengguna,
                    NEW.Waktu_Penggunaan,
                    NEW.Status
                );
            END
        ');



        DB::unprepared('
            CREATE TRIGGER insert_update_history_transaksi_ac AFTER UPDATE ON transaksi_ac
            FOR EACH ROW
            BEGIN
                INSERT INTO history_transaksi_ac (
                    id_Transaksi_AC,
                    id_ruangan,
                    id_AC,
                    Watt_AC,
                    Kecepatan_kipas,
                    Kode_hardware,
                    Kecepatan_Pendingin,
                    Mode,
                    Temp,
                    Time,
                    Swing,
                    Biaya_AC,
                    Start_waktu,
                    End_waktu,
                    Tarif_Listrik,
                    id_pengguna,
                    Waktu_Penggunaan,
                    Status
                ) VALUES (
                    NEW.id_Transaksi_AC,
                    NEW.id_ruangan,
                    NEW.id_AC,
                    NEW.Watt_AC,
                    NEW.Kecepatan_kipas,
                    NEW.Kode_hardware,
                    NEW.Kecepatan_Pendingin,
                    NEW.Mode,
                    NEW.Temp,
                    NEW.Time,
                    NEW.Swing,
                    NEW.Biaya_AC,
                    NEW.Start_waktu,
                    NEW.End_waktu,
                    NEW.Tarif_Listrik,
                    NEW.id_pengguna,
                    NEW.Waktu_Penggunaan,
                    NEW.Status
                );
            END
        ');
    }

    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS insert_id_automatis_history_transaksi_ac');
        DB::unprepared('DROP TRIGGER IF EXISTS insert_history_transaksi_ac');
        DB::unprepared('DROP TRIGGER IF EXISTS insert_update_history_transaksi_ac');
    }
}

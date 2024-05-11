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
        CREATE TRIGGER insert_id_automatis_history_transaksi_ac 
        BEFORE INSERT ON history_transaksi_ac FOR EACH ROW
        BEGIN
            DECLARE next_id INT;
            DECLARE new_id VARCHAR(20); -- Ubah panjang kolom new_id menjadi 20
        
            SELECT MAX(SUBSTRING(id_history_transaksi_AC, 6)) INTO next_id FROM history_transaksi_ac;
            
            IF next_id IS NULL THEN
                SET new_id = "HIST_000001";
            ELSE
                SET new_id = CONCAT("HIST_", LPAD(next_id + 1, 6, "0"));
            END IF;
        
            SET NEW.id_history_transaksi_AC = new_id;
        END;
    
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
                    id_tarif_listrik,
                    tarif_per_kwh,
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
                    NEw.id_tarif_listrik,
                    NEW.tarif_per_kwh,
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
                    id_tarif_listrik,
                    tarif_per_kwh,
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
                    NEW.id_tarif_listrik,
                    NEW.tarif_per_kwh,
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

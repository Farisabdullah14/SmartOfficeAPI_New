<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateTriggersAndSetAutoIdHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Membuat trigger setelah insert pada transaksi_lampu
        DB::unprepared('
            CREATE TRIGGER copy_to_history AFTER INSERT ON transaksi_lampu
            FOR EACH ROW
            BEGIN
                INSERT INTO history_transaksi_lampu (
                    id_Transaksi_lampu,
                    id_lampu,
                    Watt_lampu,
                    Kode_hardware,
                    Biaya_lampu,
                    Start_waktu,
                    End_waktu,
                    id_tarif_listrik, 
                    tarif_per_kwh,
                    id_ruangan,
                    id_pengguna,
                    Status
                ) VALUES (
                    NEW.id_Transaksi_lampu,
                    NEW.id_lampu,
                    NEW.Watt_lampu,
                    NEW.Kode_hardware,
                    NEW.Biaya_lampu,
                    NEW.Start_waktu,
                    NEW.End_waktu,
                    NEW.id_tarif_listrik, 
                    NEW.tarif_per_kwh,
                    NEW.id_ruangan,
                    NEW.id_pengguna,
                    "On"
                );
            END
        ');

        // Membuat trigger setelah update pada transaksi_lampu
        DB::unprepared('
            CREATE TRIGGER copy_update_to_history AFTER UPDATE ON transaksi_lampu
            FOR EACH ROW
            BEGIN
                INSERT INTO history_transaksi_lampu (
                    id_Transaksi_lampu,
                    id_lampu,
                    Watt_lampu,
                    Kode_hardware,
                    Biaya_lampu,
                    Start_waktu,
                    End_waktu,
                    id_tarif_listrik, 
                    tarif_per_kwh,
                    id_ruangan,
                    id_pengguna,
                    Status
                ) VALUES (
                    NEW.id_Transaksi_lampu,
                    NEW.id_lampu,
                    NEW.Watt_lampu,
                    NEW.Kode_hardware,
                    NEW.Biaya_lampu,
                    NEW.Start_waktu,
                    NEW.End_waktu,
                    NEW.id_tarif_listrik,
                    NEW.tarif_per_kwh,
                    NEW.id_ruangan,
                    NEW.id_pengguna,
                    NEW.Status
                );
            END
        ');

        // Mengubah nilai awal AUTO_INCREMENT untuk id_history_transaksi_lampu menjadi 'HTL_001'
        DB::unprepared('
        CREATE TRIGGER insert_id_automatis_history_transaksi_lampu 
        BEFORE INSERT ON history_transaksi_lampu FOR EACH ROW
        BEGIN
            DECLARE next_id INT;
            DECLARE new_id VARCHAR(20); -- Ubah panjang kolom new_id menjadi 20
        
            SELECT MAX(SUBSTRING(id_history_transaksi_lampu, 6)) INTO next_id FROM history_transaksi_lampu;
            
            IF next_id IS NULL THEN
                SET new_id = "HTL_000001";
            ELSE
                SET new_id = CONCAT("HTL_", LPAD(next_id + 1, 6, "0"));
            END IF;
        
            SET NEW.id_history_transaksi_lampu = new_id;
        END;
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Menghapus trigger
        DB::unprepared('DROP TRIGGER IF EXISTS copy_to_history');
        DB::unprepared('DROP TRIGGER IF EXISTS copy_update_to_history');
    }
}

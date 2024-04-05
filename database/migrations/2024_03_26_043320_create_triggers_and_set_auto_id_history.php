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
                    NEW.id_ruangan,
                    NEW.id_pengguna,
                    NEW.Status
                );
            END
        ');

        // Mengubah nilai awal AUTO_INCREMENT untuk id_history_transaksi_lampu menjadi 'HTL_001'
        DB::unprepared('
        CREATE TRIGGER set_id_history_transaksi BEFORE INSERT ON history_transaksi_lampu
        FOR EACH ROW
        BEGIN
            DECLARE next_id INT;
            SET next_id = (SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = "history_transaksi_lampu");
            SET NEW.id_history_transaksi_lampu = CONCAT("HTL_", LPAD(next_id, 6, "0"));
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

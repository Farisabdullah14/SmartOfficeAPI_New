<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddUpdateBiayaPenggunaanACTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
            CREATE TRIGGER update_biaya_penggunaan_AC BEFORE UPDATE ON transaksi_ac
            FOR EACH ROW
            BEGIN
                DECLARE durasi_penggunaan TIME;
                DECLARE total_energi DECIMAL(10,2);
                DECLARE biaya DECIMAL(10,2);

                -- Hitung durasi penggunaan AC
                SET durasi_penggunaan = TIMEDIFF(NEW.End_waktu, NEW.Start_waktu);

                -- Hitung total energi yang digunakan
                SET total_energi = (NEW.Watt_AC / 1000) * TIME_TO_SEC(durasi_penggunaan) / 3600;

                -- Hitung biaya penggunaan AC
              --  SET biaya = 

                 SET NEW.Biaya_AC = total_energi * NEW.Tarif_Listrik;

                 SET NEW.Waktu_Penggunaan = durasi_penggunaan;

                -- Update nilai Biaya_AC pada baris yang bersangkutan
                -- Anda tidak perlu melakukan operasi UPDATE di sini
                -- Kode berikut hanya untuk memberikan contoh, seharusnya dihapus
                -- UPDATE transaksi_ac
                -- SET Biaya_AC = biaya
                -- WHERE id_Transaksi_AC = NEW.id_Transaksi_AC;
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
        DB::unprepared('DROP TRIGGER IF EXISTS update_biaya_penggunaan_AC');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiAcModel extends Model
{
    protected $table = 'transaksi_ac';

    protected $fillable = [
        'id',
        'id_Transaksi_AC',
        'id_ruangan',
        'id_AC',
        'Watt_AC',
        'Kecepatan_kipas',
        'Kode_hardware',
        'Kecepatan_Pendingin',
        'Mode',
        'Temp',
        'Time',
        'Swing',
        'Biaya_AC',
        'Start_waktu',
        'End_waktu',
        'id_tarif_listrik',
        'tarif_per_kwh',
        'id_pengguna',
        'Waktu_Penggunaan',
        'Status',
    ];
    public $incrementing = true; // Atau false, sesuaikan dengan kebutuhan

}
    //   
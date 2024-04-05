<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryTransaksiAC extends Model
{
    use HasFactory;

    protected $table = 'history_transaksi_ac';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id_history_transaksi_AC',
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
        'Tarif_Listrik',
        'id_pengguna',
        'Waktu_Penggunaan',
        'Status',
    ];

    protected $casts = [
        'Start_waktu' => 'datetime',
        'End_waktu' => 'datetime',
    ];
}

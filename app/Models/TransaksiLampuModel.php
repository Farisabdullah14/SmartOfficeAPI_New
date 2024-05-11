<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiLampuModel extends Model
{
    use HasFactory;
    protected $table = 'transaksi_lampu';
    

    protected $fillable = [
        'id',
        'id_Transaksi_lampu',
        'Watt_lampu',
        'Kode_hardware',
        'Biaya_lampu',
        'Start_waktu',
        'End_waktu',
        'id_listrik',
        'tarif_per_kwh',
        'id_ruangan',
        'name',
        'Status',
        'id_pengguna'
    ];


    // Specify that 'id' is a bigint primary key
    protected $primaryKey = 'id';
    
}

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
        'Kecepatan_Pendingin',
        'Mode',
        'Temp',
        'Time',
        'Swing',
        'Biaya_lampu',
        'Start_waktu',
        'End_waktu',
        'Date',
        'Status',
    ];}
    //   
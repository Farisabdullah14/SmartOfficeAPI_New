<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KunciRuanganEmergency extends Model
{
    use HasFactory;
    protected $table = 'kunci_ruangan_emergency';

    protected $fillable = [
        'id_kunci_ruangan_emergency',
        'id_ruangan',
        'url_ruangan',
        'status',
        'id_user',
        'pin_ruangan',
        'id_ruangan_transaksi',
        'updated_at', // Tambahkan ini
        'created_at', // dan ini

    ];
    public $timestamps = false; // Nonaktifkan timestamps

}

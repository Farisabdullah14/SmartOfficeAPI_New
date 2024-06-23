<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RuanganTransaksi extends Model
{
    use HasFactory;

   
    protected $table = 'ruangan_transaksi';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_ruangan_transaksi',
        'id_ruangan',
        'status',
        'start_time',
        'end_time',
        'user_id',
        'jumlah_orang_yang_hadir',
        'pin_ruangan',
    ];


    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'id_ruangan', 'id');
    }
}

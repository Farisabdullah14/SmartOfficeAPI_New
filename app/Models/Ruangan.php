<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ruangan extends Model
{
    use HasFactory;
 

    protected $table = 'ruangan';

    protected $fillable = [
        'id',
        'id_ruangan',
        'nama_ruangan',
        'status',
        'door_lock_url',
    ];

    protected $primaryKey = 'id';
    public function transaksi()
    {
        return $this->hasMany(RuanganTransaksi::class, 'id_ruangan');
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AC extends Model
{
    use HasFactory;

    protected $table = 'ac'; // Menyebutkan nama tabel yang terkait dengan model

    protected $fillable = [
        'id',
        'id_AC',
        'jenis_ac',
        'watt_ac',
        'kode_hardware',
        'id_ruangan',
        'daya_va',
        'paard_kracht',
        'Status',
    ];

        // // Definisikan hubungan dengan model Ruangan
        // public function ruangan()
        // {
        //     return $this->belongsTo(Ruangan::class, 'id_ruangan', 'id');
        // }
    
}

<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LampuModel extends Model
{
    use HasFactory;
    protected $table = 'lampu';


    protected $fillable = [
        'id',
        'id_lampu',
        'jenis_lampu',
        'watt_lampu',
        'Kode_hardware',
        'id_ruangan',
        'keterangan', 
        'image', 
    ];



    
    protected $primaryKey = 'id';
}

    
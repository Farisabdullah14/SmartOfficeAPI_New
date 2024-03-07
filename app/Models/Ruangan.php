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
        'status'
    ];

    protected $primaryKey = 'id';

}
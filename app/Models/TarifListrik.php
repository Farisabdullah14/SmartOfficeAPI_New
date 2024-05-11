<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TarifListrik extends Model
{
    protected $table = 'tarif_listrik';

    protected $fillable = [
        'kode_tarif',
        'daya',
        'tarif_per_kwh',
        'golongan',
    ];

    // Timestamps
    public $timestamps = true;

}

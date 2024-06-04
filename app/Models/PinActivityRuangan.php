<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PinActivityRuangan extends Model
{
    use HasFactory;

    protected $table = 'pin_activity_ruangan';

    protected $fillable = [
        'id_pin_activity_ruangan',
        'id_ruangan_transaksi',
        'id_ruangan',
        'start_time',
        'end_time',
        'user_id',
        'pin_ruangan',
    ];

    // Tambahkan relasi jika diperlukan
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

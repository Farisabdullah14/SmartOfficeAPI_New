<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TransaksiLampuModel; // Tambahkan baris ini

class HistoryTransaksiLampu extends Model
{
    use HasFactory;
    protected $table = 'history_transaksi_lampu';

    protected $fillable = [
        'id_transaksi_lampu',
        'watt_lampu',
        'kode_hardware',
        'biaya_lampu',
        'start_waktu',
        'end_waktu',
        'id_listrik',
        'tarif_per_kwh',
        'id_ruangan',
        'name',
        'status',
        'id_pengguna'
    ];  
    public function transaksiLampu()
    {
        return $this->belongsTo(TransaksiLampuModel::class, 'id_transaksi_lampu');
    }

}

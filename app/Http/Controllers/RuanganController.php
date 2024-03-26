<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Models\TransaksiLampuModel;

use Illuminate\Http\Request;
use App\Models\Ruangan;
class RuanganController extends Controller
{



    public function ALLDataRuangan()
    {
        // $ruangan = Ruangan::all();
        // return response()->json($ruangan);
      
        
        $ruangan = Ruangan::select('id_ruangan', 'nama_ruangan', 'status')->get();

        $ruanganData = $ruangan->map(function ($item) {
            $lampuCount = DB::table('lampu')->where('id_ruangan', $item->id_ruangan)->count();
            $acCount = DB::table('ac')->where('id_ruangan', $item->id_ruangan)->count();
            $item['jumlah_perangkat'] = $lampuCount + $acCount;
            return $item;
        });
        return response()->json($ruanganData);
    }

    public function getjumlahDV($idRuangan)
    {
        $lampuCount = DB::table('lampu')
        ->where('id_ruangan', $idRuangan)
        ->count();
    
        $acCount = DB::table('ac')
        ->where('id_ruangan', $idRuangan)
        ->count();
    
        $totalDevicesCount = $lampuCount + $acCount;
        return response()->json([$totalDevicesCount]);
    }

    public function getDataByIdRuangan($idRuangan)
    {
    $lampuData = DB::table('Lampu')
                    ->select('id_lampu', 'jenis_lampu', 'watt_lampu','Kode_hardware','id_ruangan',)
                    ->where('id_ruangan', $idRuangan)
                    ->get();

    $acData = DB::table('ac')
                ->select('id_AC', 'jenis_ac', 'watt_ac', 'daya_va', 'paard_kracht','Kode_hardware')
                ->where('id_ruangan', $idRuangan)
                ->get();

    $mergedData = array_merge($lampuData->toArray(), $acData->toArray());

    return response()->json($mergedData);
    }



    public function ambilDataDanGabungkan(Request $request, $idRuangan)
{
    $lampuData = DB::table('Lampu')
                ->select('id_lampu', 'jenis_lampu', 'watt_lampu', 'Kode_hardware', 'id_ruangan')
                ->where('id_ruangan', $idRuangan)
                ->get();

    // Ambil data AC
    $acData = DB::table('ac')
                ->select('id_AC', 'jenis_ac', 'watt_ac', 'daya_va', 'paard_kracht', 'Kode_hardware')
                ->where('id_ruangan', $idRuangan)
                ->get();

    // Ambil data transaksi lampu terbaru
    $latestTransaksiLampu = TransaksiLampuModel::where('id_ruangan', $idRuangan)
                                                ->where('Status', 'on')
                                                ->latest()
                                                ->first();

    // Tambahkan id_pengguna dari transaksi lampu terbaru
    $id_pengguna_transaksi = $latestTransaksiLampu ? $latestTransaksiLampu->id_pengguna : null;

    // Tambahkan status dari lampu pada data lampu
    $lampuData = $lampuData->map(function($lampu) use ($latestTransaksiLampu, $id_pengguna_transaksi) {
        if ($latestTransaksiLampu && $lampu->id_lampu === $latestTransaksiLampu->id_lampu) {
            $lampu->status = $latestTransaksiLampu->Status;
            $lampu->id_pengguna = $id_pengguna_transaksi;
        } else {
            $lampu->status = 'Off';
            $lampu->id_pengguna = null;
        }
        return $lampu;
    });

    // Format data untuk respons JSON
    $mergedData = array_merge($lampuData->toArray(), $acData->toArray());
    return response()->json($mergedData);
}


//     public function ambilDataDanGabungkan(Request $request, $idRuangan)
// {
//     // Ambil data lampu
// }


}

// return response()->json([
//     'perangkatList' => [
//         $lampuData,
//         $acData
//     ]
// ]);
<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Models\TransaksiLampuModel;
use App\Models\TransaksiAcModel;
use Carbon\Carbon;

use Illuminate\Http\Request;
use App\Models\Ruangan;
use App\Models\RuanganTransaksi;
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



//     public function ambilDataDanGabungkan(Request $request, $idRuangan)
// {
//     $lampuData = DB::table('Lampu')
//                 ->select('id_lampu', 'jenis_lampu', 'watt_lampu', 'Kode_hardware', 'id_ruangan')
//                 ->where('id_ruangan', $idRuangan)
//                 ->get();

//     // Ambil data AC
//     $acData = DB::table('ac')
//                 ->select('id_AC', 'jenis_ac', 'watt_ac', 'daya_va', 'paard_kracht', 'Kode_hardware')
//                 ->where('id_ruangan', $idRuangan)
//                 ->get();

//     // Ambil data transaksi lampu terbaru
//     $latestTransaksiLampu = TransaksiLampuModel::where('id_ruangan', $idRuangan)
//                                                 ->where('Status', 'on')
//                                                 ->latest()
//                                                 ->first();

//     // Tambahkan id_pengguna dari transaksi lampu terbaru
//     $id_pengguna_transaksi = $latestTransaksiLampu ? $latestTransaksiLampu->id_pengguna : null;

//     // Tambahkan status dari lampu pada data lampu
//     $lampuData = $lampuData->map(function($lampu) use ($latestTransaksiLampu, $id_pengguna_transaksi) {
//         if ($latestTransaksiLampu && $lampu->id_lampu === $latestTransaksiLampu->id_lampu) {
//             $lampu->status = $latestTransaksiLampu->Status;
//             $lampu->id_pengguna = $id_pengguna_transaksi;
//         } else {
//             $lampu->status = 'Off';
//             $lampu->id_pengguna = null;
//         }
//         return $lampu;
//     });

//     // Format data untuk respons JSON
//     $mergedData = array_merge($lampuData->toArray(), $acData->toArray());
//     return response()->json($mergedData);
// }


// public function getRuanganWithTransaksi(Request $request, $user_id)
// {
//     // Mengambil data ruangan dengan transaksi berdasarkan user_id
//     $ruanganData = Ruangan::with(['transaksi' => function ($query) use ($user_id) {
//         $query->where('user_id', $user_id);
//     }])->get();

//     // Format data yang diinginkan
//     $formattedData = $ruanganData->map(function ($ruangan) {
//         // Jika tidak ada transaksi, set status_ruangan, start_time, dan end_time menjadi null
//         if ($ruangan->transaksi->isEmpty()) {
//             return [
//                 'id_ruangan' => $ruangan->id_ruangan,
//                 'nama_ruangan' => $ruangan->nama_ruangan,
//                 'status_ruangan' => null,
//                 'start_time' => null,
//                 'end_time' => null
//             ];
//         }

//         // Jika ada transaksi, ambil data transaksi pertama
//         $transaksi = $ruangan->transaksi->first();

//         return [
//             'id_ruangan' => $ruangan->id_ruangan,
//             'nama_ruangan' => $ruangan->nama_ruangan,
//             'status_ruangan' => $transaksi->status,
//             'start_time' => $transaksi->start_time,
//             'end_time' => $transaksi->end_time
//         ];
//     });

//     return response()->json($formattedData);
// }

// public function getRuanganWithTransaksi(Request $request, $userId)
// {
//     // Subquery untuk mendapatkan transaksi terbaru per id_ruangan
//     $subQuery = DB::table('ruangan_transaksi as rt1')
//                   ->select('rt1.id_ruangan', DB::raw('MAX(rt1.id) as max_id'))
//                   ->groupBy('rt1.id_ruangan');

//     // Mengambil data ruangan dengan transaksi terbaru berdasarkan user_id
//     $ruanganData = DB::table('ruangan')
//                     ->leftJoinSub($subQuery, 'rt_sub', function($join) {
//                         $join->on('ruangan.id_ruangan', '=', 'rt_sub.id_ruangan');
//                     })
//                     ->leftJoin('ruangan_transaksi', function($join) use ($userId) {
//                         $join->on('rt_sub.max_id', '=', 'ruangan_transaksi.id')
//                              ->where('ruangan_transaksi.user_id', '=', $userId);
//                     })
//                     ->select(
//                         'ruangan.id_ruangan',
//                         'ruangan.nama_ruangan',
//                         'ruangan.status as status_ruangan',
//                         'ruangan_transaksi.id as transaksi_id',  // tambahkan id transaksi untuk update
//                         'ruangan_transaksi.status as status_transaksi',
//                         'ruangan_transaksi.user_id',
//                         'ruangan_transaksi.start_time',
//                         'ruangan_transaksi.end_time'
//                     )
//                     ->get();

//     // Menambahkan jumlah perangkat pada setiap ruangan dan memeriksa waktu end_time
//     $ruanganData = $ruanganData->map(function ($item) {
//         $lampuCount = DB::table('lampu')->where('id_ruangan', $item->id_ruangan)->count();
//         $acCount = DB::table('ac')->where('id_ruangan', $item->id_ruangan)->count();
//         $item->jumlah_perangkat = $lampuCount + $acCount;

//         // Memeriksa dan mengupdate status transaksi berdasarkan end_time
//         if ($item->end_time && Carbon::parse($item->end_time)->lessThan(Carbon::now())) {
//             $item->status_transaksi = 'Off';
//             // Mengupdate status_transaksi di database
//             DB::table('ruangan_transaksi')
//                 ->where('id', $item->transaksi_id)
//                 ->update(['status' => 'Off']);
//         }

//         return $item;
//     });

//     return response()->json($ruanganData);
// }

// public function getRuanganWithTransaksi(Request $request, $userId)
// {
//     // Subquery untuk mendapatkan transaksi terbaru per id_ruangan
//     $subQuery = DB::table('ruangan_transaksi as rt1')
//                   ->select('rt1.id_ruangan', DB::raw('MAX(rt1.id) as max_id'))
//                   ->groupBy('rt1.id_ruangan');

//     // Mengambil data ruangan dengan transaksi terbaru berdasarkan user_id
//     $ruanganData = DB::table('ruangan')
//                     ->leftJoinSub($subQuery, 'rt_sub', function($join) {
//                         $join->on('ruangan.id_ruangan', '=', 'rt_sub.id_ruangan');
//                     })
//                     ->leftJoin('ruangan_transaksi', function($join) use ($userId) {
//                         $join->on('rt_sub.max_id', '=', 'ruangan_transaksi.id')
//                              ->where('ruangan_transaksi.user_id', '=', $userId);
//                     })
//                     ->select(
//                         'ruangan.id_ruangan',
//                         'ruangan.nama_ruangan',
//                         'ruangan.status as status_ruangan',
//                         'ruangan_transaksi.id as transaksi_id',  // tambahkan id transaksi untuk update
//                         'ruangan_transaksi.status as status_transaksi',
//                         'ruangan_transaksi.user_id',
//                         'ruangan_transaksi.start_time',
//                         'ruangan_transaksi.end_time'
//                     )
//                     ->get();

//     // Menambahkan jumlah perangkat pada setiap ruangan dan memeriksa waktu end_time
//     $ruanganData = $ruanganData->map(function ($item) {
//         $lampuCount = DB::table('lampu')->where('id_ruangan', $item->id_ruangan)->count();
//         $acCount = DB::table('ac')->where('id_ruangan', $item->id_ruangan)->count();
//         $item->jumlah_perangkat = $lampuCount + $acCount;

//         // Memeriksa dan mengupdate status transaksi berdasarkan end_time
//         if ($item->end_time && Carbon::parse($item->end_time)->lessThan(Carbon::now())) {
//             $item->status_transaksi = 'Off';
//             // Mengupdate status_transaksi di database
//             DB::table('ruangan_transaksi')
//                 ->where('id', $item->transaksi_id)
//                 ->update(['status' => 'Off']);
//         }

//         // Menghapus kolom start_time dan end_time
//         unset($item->start_time, $item->end_time , $item->transaksi_id);

//         return $item;
//     });

//     return response()->json($ruanganData);
// }


public function getRuanganWithTransaksi(Request $request, $userId)
{
    // Subquery untuk mendapatkan transaksi terbaru per id_ruangan
    $subQuery = DB::table('ruangan_transaksi as rt1')
                  ->select('rt1.id_ruangan', DB::raw('MAX(rt1.id) as max_id'))
                  ->groupBy('rt1.id_ruangan');

    // Mengambil data ruangan dengan transaksi terbaru berdasarkan user_id
    $ruanganData = DB::table('ruangan')
                    ->leftJoinSub($subQuery, 'rt_sub', function($join) {
                        $join->on('ruangan.id_ruangan', '=', 'rt_sub.id_ruangan');
                    })
                    ->leftJoin('ruangan_transaksi', 'rt_sub.max_id', '=', 'ruangan_transaksi.id')
                    ->leftJoin('pin_activity_ruangan', function($join) use ($userId) {
                        $join->on('ruangan_transaksi.id_ruangan_transaksi', '=', 'pin_activity_ruangan.id_ruangan_transaksi')
                             ->where('pin_activity_ruangan.user_id', '=', $userId);
                    })
                    ->select(
                        'ruangan.id_ruangan',
                        'ruangan.nama_ruangan',
                        'ruangan.status as status_ruangan',
                        'ruangan_transaksi.status as status_transaksi',
                        'pin_activity_ruangan.user_id'
                    )
                    ->get();

    // Menambahkan jumlah perangkat pada setiap ruangan
    $ruanganData = $ruanganData->map(function ($item) {
        $lampuCount = DB::table('lampu')->where('id_ruangan', $item->id_ruangan)->count();
        $acCount = DB::table('ac')->where('id_ruangan', $item->id_ruangan)->count();
        $item->jumlah_perangkat = $lampuCount + $acCount;

        return $item;
    });

    return response()->json($ruanganData);
}






public function ambilDataDanGabungkan(Request $request, $idRuangan)
{
    // Ambil data lampu dari tabel Lampu
    $lampuData = DB::table('Lampu')
                ->select('id_lampu', 'jenis_lampu', 'watt_lampu', 'Kode_hardware', 'id_ruangan')
                ->where('id_ruangan', $idRuangan)
                ->get();

    // Ambil data AC dari tabel AC
    $acData = DB::table('ac')
                ->select('id_AC', 'jenis_ac', 'watt_ac', 'daya_va', 'paard_kracht', 'Kode_hardware')
                ->where('id_ruangan', $idRuangan)
                ->get();

    // Ambil data transaksi lampu terbaru
    $latestTransaksiLampu = TransaksiLampuModel::where('id_ruangan', $idRuangan)
                                                ->where('Status', 'on')
                                                ->latest()
                                                ->first();

    // Ambil data transaksi AC terbaru
    $latestTransaksiAC = TransaksiAcModel::where('id_ruangan', $idRuangan)
                                                ->where('Status', 'on')
                                                ->latest()
                                                ->first();

    // Tambahkan id_pengguna dari transaksi lampu terbaru
    $id_pengguna_transaksi = $latestTransaksiLampu ? $latestTransaksiLampu->id_pengguna : null;

    // Tambahkan id_pengguna dari transaksi AC terbaru
    $id_pengguna_transaksi_AC = $latestTransaksiAC ? $latestTransaksiAC->id_pengguna : null;

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

    // Tambahkan status dari AC pada data AC
    $acData = $acData->map(function($ac) use ($latestTransaksiAC, $id_pengguna_transaksi_AC) {
        if ($latestTransaksiAC && $ac->id_AC === $latestTransaksiAC->id_AC) {
            $ac->status = $latestTransaksiAC->Status;
            $ac->id_pengguna = $id_pengguna_transaksi_AC;
        } else {
            $ac->status = 'Off';
            $ac->id_pengguna = null;
        }
        return $ac;
    });

    // Gabungkan data lampu dan AC
    $mergedData = array_merge($lampuData->toArray(), $acData->toArray());

    // Format data untuk respons JSON
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
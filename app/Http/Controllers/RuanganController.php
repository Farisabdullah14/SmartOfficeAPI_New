<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Models\TransaksiLampuModel;
use App\Models\TransaksiAcModel;
use App\Events\LampuStatusChanged;
use Carbon\Carbon;
use App\Models\PinActivityRuangan;
use Illuminate\Support\Facades\Validator; // Tambahkan ini
use Illuminate\Support\Facades\Http; // Tambahkan ini



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


// public function getRuanganWithTransaksi(Request $request, $userId)
// {
//     $ruanganData = DB::table('ruangan')
//                     ->leftJoin('ruangan_transaksi', function($join) {
//                         $join->on('ruangan.id_ruangan', '=', 'ruangan_transaksi.id_ruangan')
//                              ->whereRaw('ruangan_transaksi.id = (select max(id) from ruangan_transaksi where ruangan_transaksi.id_ruangan = ruangan.id_ruangan)');
//                     })
//                     ->select(
//                         'ruangan.id_ruangan',
//                         'ruangan.nama_ruangan',
//                         'ruangan.status as status_ruangan',
//                         'ruangan_transaksi.status as status_transaksi',
//                         'ruangan_transaksi.id_ruangan_transaksi' // tambahkan properti id_ruangan_transaksi
//                     )
//                     ->get();

//     // Menambahkan jumlah perangkat pada setiap ruangan
//     $ruanganData = $ruanganData->map(function ($item) {
//         $lampuCount = DB::table('lampu')->where('id_ruangan', $item->id_ruangan)->count();
//         $acCount = DB::table('ac')->where('id_ruangan', $item->id_ruangan)->count();
//         $item->jumlah_perangkat = $lampuCount + $acCount;

//         return $item;
//     });

//     // Mengambil data dari pin_activity_ruangan
//     foreach ($ruanganData as $ruangan) {
//         $pinActivity = DB::table('pin_activity_ruangan')
//                         ->where('id_ruangan_transaksi', $ruangan->id_ruangan_transaksi ?? null)
//                         ->where('user_id', $userId)
//                         ->first();

//         $ruangan->user_id = $pinActivity ? $pinActivity->user_id : null;
//     }

//     // Mengubah format data sesuai dengan kebutuhan output
//     $formattedData = $ruanganData->map(function ($item) {
//         return [
//             'id_ruangan' => $item->id_ruangan,
//             'nama_ruangan' => $item->nama_ruangan,
//             'status_ruangan' => $item->status_ruangan,
//             'status_transaksi' => $item->status_transaksi ?: null,
//             'user_id' => $item->user_id ?: null,
//             'jumlah_perangkat' => $item->jumlah_perangkat,
//         ];
//     });

//     return response()->json($formattedData);
// }



public function getRuanganWithTransaksi(Request $request, $userId)
{
    $twoHoursAgo = now()->subHours(2)->format('Y-m-d H:i:s');

    $ruanganData = DB::table('ruangan')
                    ->leftJoin('ruangan_transaksi', function($join) use ($twoHoursAgo) {
                        $join->on('ruangan.id_ruangan', '=', 'ruangan_transaksi.id_ruangan')
                             ->whereRaw('ruangan_transaksi.id = (select max(id) from ruangan_transaksi where ruangan_transaksi.id_ruangan = ruangan.id_ruangan and ruangan_transaksi.end_time >= ?)', [$twoHoursAgo]);
                    })
                    ->select(
                        'ruangan.id_ruangan',
                        'ruangan.nama_ruangan',
                        'ruangan.status as status_ruangan',
                        'ruangan_transaksi.status as status_transaksi',
                        'ruangan_transaksi.id_ruangan_transaksi'
                    )
                    ->get();



                    

    // Menambahkan jumlah perangkat pada setiap ruangan
    $ruanganData = $ruanganData->map(function ($item) {
        $lampuCount = DB::table('lampu')->where('id_ruangan', $item->id_ruangan)->count();
        $acCount = DB::table('ac')->where('id_ruangan', $item->id_ruangan)->count();
        $item->jumlah_perangkat = $lampuCount + $acCount;

        return $item;
    });

    // Mengambil data dari pin_activity_ruangan
    foreach ($ruanganData as $ruangan) {
        $pinActivity = DB::table('pin_activity_ruangan')
                        ->where('id_ruangan_transaksi', $ruangan->id_ruangan_transaksi ?? null)
                        ->where('user_id', $userId)
                        ->first();

        $ruangan->user_id = $pinActivity ? $pinActivity->user_id : null;
    }

    // Mengubah format data sesuai dengan kebutuhan output
    $formattedData = $ruanganData->map(function ($item) {
        return [
            'id_ruangan' => $item->id_ruangan,
            'nama_ruangan' => $item->nama_ruangan,
            'status_ruangan' => $item->status_ruangan,
            'status_transaksi' => $item->status_transaksi ?: null,
            'user_id' => $item->user_id ?: null,
            'jumlah_perangkat' => $item->jumlah_perangkat,
        ];
    });

    return response()->json($formattedData);
}



public function SearchAndCreatePinActivity(Request $request)
{
    // Gunakan waktu sekarang dalam format yang sama dengan waktu di database
    $currentTime = Carbon::now()->format('Y-m-d H:i:s');

    // Validasi input
    $validator = Validator::make($request->all(), [
        'id_pin' => 'required|string|max:255',
        'user_id' => 'required|integer',
        'id_ruangan' => 'required|string|max:255',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 400);
    }

    $id_pin = $request->input('id_pin');
    $user_id = $request->input('user_id');
    $id_ruangan = $request->input('id_ruangan');

    // Cari ruangan
    $ruangan = Ruangan::find($id_ruangan);
    if (!$ruangan || !$ruangan->door_lock_url) {
        return response()->json(['message' => 'Ruangan tidak ditemukan atau URL door lock tidak tersedia'], 404);
    }

    // Cari ruangan transaksi terbaru berdasarkan id_ruangan dan pin_ruangan
    $ruanganTransaksi = RuanganTransaksi::where('id_ruangan', $id_ruangan)
                                        ->where('pin_ruangan', $id_pin)
                                        ->orderBy('created_at', 'desc')
                                        ->first();

    if (!$ruanganTransaksi) {
        return response()->json(['message' => 'Ruangan transaksi tidak ditemukan'], 404);
    }

    // Validasi waktu start_time dan end_time
    if ($ruanganTransaksi->start_time > $currentTime) {
        return response()->json(['message' => 'Waktu start belum dimulai, tidak bisa menambah data'], 400);
    }

    if ($ruanganTransaksi->end_time <= $currentTime) {
        return response()->json(['message' => 'Waktu end sudah berlalu, tidak bisa menambah data'], 400);
    }

    // Periksa apakah user_id sudah pernah melakukan aktivitas pin di ruangan ini sebelumnya
    $existingActivity = PinActivityRuangan::where('id_ruangan_transaksi', $ruanganTransaksi->id_ruangan_transaksi)
                                          ->where('user_id', $user_id)
                                          ->first();

    if ($existingActivity) {
        return response()->json(['message' => 'User ini sudah melakukan aktivitas pin di ruangan ini sebelumnya'], 400);
    }

    // Hitung jumlah pin ruangan yang sudah diaktifkan
    $jumlah_pin = PinActivityRuangan::where('id_ruangan_transaksi', $ruanganTransaksi->id_ruangan_transaksi)->count();

    // Jika jumlah pin ruangan sudah 2 kali atau lebih, kembalikan pesan error
    if ($jumlah_pin >= 2) {
        return response()->json(['message' => 'Maaf, pin ruangannya sudah diaktifkan lebih dari 2 kali'], 400);
    }

    // Buat record baru di tabel pin_activity_ruangan
    $pinActivityRuangan = new PinActivityRuangan();
    $pinActivityRuangan->id_ruangan_transaksi = $ruanganTransaksi->id_ruangan_transaksi;
    $pinActivityRuangan->id_ruangan = $ruanganTransaksi->id_ruangan;
    $pinActivityRuangan->start_time = $ruanganTransaksi->start_time;
    $pinActivityRuangan->end_time = $ruanganTransaksi->end_time;
    $pinActivityRuangan->user_id = $user_id;
    $pinActivityRuangan->pin_ruangan = $ruanganTransaksi->pin_ruangan;
    $pinActivityRuangan->save();

    // Trigger ke hardware door lock
    $response = Http::get("{$ruangan->door_lock_url}/API/{$id_ruangan}/On");

    // Periksa respons dari door lock API
    if ($response->failed()) {
        return response()->json(['message' => 'Gagal mengaktifkan door lock'], 500);
    }

    return response()->json([
        'message' => 'Pin activity ruangan berhasil ditambahkan dan door lock diaktifkan',
        'data' => $pinActivityRuangan
    ], 201);
}
public function getJumlahRuanganDigunakanHariIni(Request $request, $userId)
{
    // Mendapatkan tanggal hari ini dalam format YYYY-MM-DD
    $today = date('Y-m-d');

    // Menghitung jumlah ruangan yang memiliki transaksi terkait pada hari ini dan terkait dengan user tertentu
    $jumlahRuanganDigunakanHariIni = DB::table('ruangan')
                                        ->leftJoin('ruangan_transaksi', 'ruangan.id_ruangan', '=', 'ruangan_transaksi.id_ruangan')
                                        ->leftJoin('pin_activity_ruangan', 'ruangan_transaksi.id_ruangan_transaksi', '=', 'pin_activity_ruangan.id_ruangan_transaksi')
                                        ->whereNotNull('pin_activity_ruangan.id_pin_activity_ruangan')
                                        ->where('pin_activity_ruangan.user_id', '=', $userId)
                                        ->whereDate('ruangan_transaksi.start_time', '=', $today)
                                        ->orWhereDate('ruangan_transaksi.end_time', '=', $today)
                                        ->distinct()
                                        ->count('ruangan.id_ruangan');

    // return response()->json(['jumlah_ruangan_digunakan_hari_ini' => $jumlahRuanganDigunakanHariIni]);
    return  $jumlahRuanganDigunakanHariIni;
}

  public function cekStatusRuangan($id_ruangan)
    {
        $ruangan = Ruangan::where('id_ruangan', $id_ruangan)->first();

        if ($ruangan) {
            return response()->json([
                // 'id_ruangan' => $ruangan->id_ruangan,
                'nama_ruangan' => $ruangan->nama_ruangan,
                'status_ruangan' => $ruangan->status,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Ruangan tidak ditemukan'
            ], 404);
        }
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

    // Ambil data transaksi lampu terbaru berdasarkan id_lampu
    $latestTransaksiLampu = collect([]);
    foreach ($lampuData as $lampu) {
        $latestTransaksi = TransaksiLampuModel::where('id_ruangan', $idRuangan)
                                                ->where('id_lampu', $lampu->id_lampu)
                                                ->latest()
                                                ->first();
        // Tambahkan ke koleksi
        if ($latestTransaksi) {
            $latestTransaksiLampu->push($latestTransaksi);
        }
    }

    // Ambil data transaksi AC terbaru
    $latestTransaksiAC = TransaksiAcModel::where('id_ruangan', $idRuangan)
                                                ->latest()
                                                ->first();

    // Tambahkan status dari lampu pada data lampu
    $lampuData = $lampuData->map(function($lampu) use ($latestTransaksiLampu) {
        $lampu->status = 'Off'; // Set default status to Off

        // Cek apakah ada transaksi lampu terbaru
        if ($latestTransaksiLampu->isNotEmpty()) {
            // Jika ada transaksi lampu terbaru dengan status On dan id_lampu yang sesuai
            $latestTransaksi = $latestTransaksiLampu->where('id_lampu', $lampu->id_lampu)->first();
            if ($latestTransaksi && $latestTransaksi->Status === 'On') {
                $lampu->status = 'On';
            }
        }

        return $lampu;
    });

    // Tambahkan status dari AC pada data AC
    $acData = $acData->map(function($ac) use ($latestTransaksiAC) {
        $ac->status = 'Off'; // Set default status to Off

        // Jika ada transaksi AC terbaru dengan status On
        if ($latestTransaksiAC && $latestTransaksiAC->Status === 'On') {
            $ac->status = 'On';
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
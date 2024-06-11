<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use App\Models\RuanganTransaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\PinActivityRuanga;
use App\Models\PinActivityRuangan;
class RuanganTransaksiController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
{
    // Validasi input
    $validator = Validator::make($request->all(), [
        'status' => 'string|max:255',
        'start_time' => 'required|date',
        'end_time' => 'required|date',
        'user_id' => 'required|integer',
        'jumlah_orang_yang_hadir' => 'required|string|max:255',
        'id_ruangan' => 'string|max:255',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 400);
    }

    // Tetapkan nilai id_ruangan jika tersedia
    $id_ruangan = $request->input('id_ruangan', null);

    // Generate random pin_ruangan with maximum 5 digits
    $pin_ruangan = str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);

    // Buat record baru di tabel ruangan_transaksi
    $ruanganTransaksi = new RuanganTransaksi();
    $ruanganTransaksi->id_ruangan = $id_ruangan;
    $ruanganTransaksi->status = $request->input('status', 'on');
    $ruanganTransaksi->start_time = $request->input('start_time');
    $ruanganTransaksi->end_time = $request->input('end_time');
    $ruanganTransaksi->user_id = $request->input('user_id');
    $ruanganTransaksi->jumlah_orang_yang_hadir = $request->input('jumlah_orang_yang_hadir');
    $ruanganTransaksi->pin_ruangan = $pin_ruangan;
    $ruanganTransaksi->save();

    return response()->json([
        'message' => 'Ruangan transaksi berhasil ditambahkan',
        'data' => $ruanganTransaksi
    ], 201);
}



// public function CreateTransaksiRuanganSementara(Request $request)
// {
//     $currentTime = Carbon::now();
//     $startTime = $currentTime->toDateTimeString();

//     // Validasi input
//     $validator = Validator::make($request->all(), [
//         'status' => 'string|max:255',
//         'user_id' => 'required|integer',
//         'id_ruangan' => 'string|max:255',
//     ]);

//     if ($validator->fails()) {
//         return response()->json($validator->errors(), 400);
//     }

//     // Tetapkan nilai id_ruangan jika tersedia
//     $id_ruangan = $request->input('id_ruangan', null);

//     // Generate random pin_ruangan with maximum 5 digits
//     $pin_ruangan = str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);

//     // Buat record baru di tabel ruangan_transaksi
//     $ruanganTransaksi = new RuanganTransaksi();
//     $ruanganTransaksi->id_ruangan = $id_ruangan;
//     $ruanganTransaksi->status = $request->input('status', 'on');
//     // $ruanganTransaksi->start_time = $request->input('start_time');
//     $ruanganTransaksi->start_time = $startTime;
//     $ruanganTransaksi->user_id = $request->input('user_id');
//     $ruanganTransaksi->pin_ruangan = $pin_ruangan;
//     $ruanganTransaksi->save();

//     return response()->json([
//         'message' => 'Ruangan transaksi berhasil ditambahkan',
//         'data' => $ruanganTransaksi
//     ], 201);
// }
public function CreateTransaksiRuanganSementara(Request $request)
{

    // $decryptedData = $request->all();
    // foreach ($decryptedData as $key => $value) {
    //     $decryptedData[$key] = md5($value); // Mendekripsi nilai dengan MD5
    // }

    $currentTime = Carbon::now();
    $startTime = $currentTime->toDateTimeString();
    
    // Validasi input
    $validator = Validator::make($request->all(), [
        'status' => 'string|max:255',
        'user_id' => 'required|integer',
        'id_ruangan' => 'required|string|max:255',
    ]);
    
    if ($validator->fails()) {
        return response()->json($validator->errors(), 400);
    }
    
    $id_ruangan = $request->input('id_ruangan');
    $user_id = $request->input('user_id');
    
    // Generate random pin_ruangan with maximum 5 digits
    $pin_ruangan = str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
    
    // Cari ruangan_transaksi dengan status 'on'
    $ruanganTransaksi = RuanganTransaksi::where('id_ruangan', $id_ruangan)
                                        ->where('status', 'on')
                                        ->first();
    
    if ($ruanganTransaksi) {
        $id_ruangan_transaksi = $ruanganTransaksi->id_ruangan_transaksi;
        
        // Menghitung jumlah pin ruangan yang sudah diinput pada waktu start_time yang sama
        $jumlah_pin = PinActivityRuangan::where('id_ruangan_transaksi', $id_ruangan_transaksi)->count();
        
        // Menghitung jumlah id_user yang berbeda dalam pin_activity_ruangan
        $jumlah_id_user = PinActivityRuangan::where('id_ruangan_transaksi', $id_ruangan_transaksi)
                                            ->distinct('user_id')
                                            ->count('user_id');
        
        // Jika user_id sudah ada dalam transaksi
        $userExists = PinActivityRuangan::where('id_ruangan_transaksi', $id_ruangan_transaksi)
                                         ->where('user_id', $user_id)
                                         ->exists();
        
        if ($userExists) {
            // Ubah status menjadi 'off' dan isi end_time
            $ruanganTransaksi->status = 'off';
            $ruanganTransaksi->end_time = $currentTime->toDateTimeString();
            $ruanganTransaksi->save();
            
            return response()->json([
                'message' => 'Ruangan transaksi berhasil dinonaktifkan karena user_id sudah ada',
                'data' => $ruanganTransaksi
            ], 200);
        }
        
        // Jika jumlah id_user yang berbeda lebih dari 2
        if ($jumlah_id_user >= 2) {
            return response()->json([
                'message' => 'Tidak bisa diakses lebih dari 2 id_user yang berbeda'
            ], 400);
        }
        
        // Jika jumlah pin ruangan sudah 2 kali atau lebih, ubah status menjadi 'off' dan isi end_time
        if ($jumlah_pin >= 2) {
            $ruanganTransaksi->status = 'off';
            $ruanganTransaksi->end_time = $currentTime->toDateTimeString();
            $ruanganTransaksi->save();
            
            return response()->json([
                'message' => 'Ruangan transaksi berhasil dinonaktifkan',
                'data' => $ruanganTransaksi
            ], 200);
        } else {
            // Buat record baru di tabel pin_activity_ruangan
            $pinActivityRuangan = new PinActivityRuangan();
            $pinActivityRuangan->id_ruangan_transaksi = $id_ruangan_transaksi;
            $pinActivityRuangan->id_ruangan = $ruanganTransaksi->id_ruangan;
            $pinActivityRuangan->start_time = $ruanganTransaksi->start_time;
            $pinActivityRuangan->end_time = null; // Set null karena transaksi masih aktif
            $pinActivityRuangan->user_id = $user_id;
            $pinActivityRuangan->pin_ruangan = $ruanganTransaksi->pin_ruangan;
            $pinActivityRuangan->save();
            
            return response()->json([
                'message' => 'Pin ruangan berhasil ditambahkan',
                'data' => $pinActivityRuangan
            ], 201);
        }
    } else {
        // Jika ruangan transaksi dengan status 'on' tidak ditemukan, buat record baru
        $ruanganTransaksi = new RuanganTransaksi();
        $ruanganTransaksi->id_ruangan = $id_ruangan;
        $ruanganTransaksi->status = $request->input('status', 'on');
        $ruanganTransaksi->start_time = $startTime;
        $ruanganTransaksi->user_id = $user_id;
        $ruanganTransaksi->pin_ruangan = $pin_ruangan;
        $ruanganTransaksi->save();
        
        // Ambil nilai id_ruangan_transaksi setelah data berhasil disimpan
        $ruanganTransaksi->refresh();
        $id_ruangan_transaksi = $ruanganTransaksi->id_ruangan_transaksi;
        
        // Buat record baru di tabel pin_activity_ruangan
        $pinActivityRuangan = new PinActivityRuangan();
        $pinActivityRuangan->id_ruangan_transaksi = $id_ruangan_transaksi;
        $pinActivityRuangan->id_ruangan = $ruanganTransaksi->id_ruangan;
        $pinActivityRuangan->start_time = $ruanganTransaksi->start_time;
        $pinActivityRuangan->end_time = null; // Set null karena transaksi baru
        $pinActivityRuangan->user_id = $user_id;
        $pinActivityRuangan->pin_ruangan = $pin_ruangan;
        $pinActivityRuangan->save();
        
        return response()->json([
            'message' => 'Ruangan transaksi dan pin ruangan berhasil ditambahkan',
            'data_ruangan_transaksi' => $ruanganTransaksi,
            'data_pin_activity_ruangan' => $pinActivityRuangan
        ], 201);
    }
}


}
    


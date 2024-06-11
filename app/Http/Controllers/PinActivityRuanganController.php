<?php

namespace App\Http\Controllers;

use App\Models\PinActivityRuangan;
use App\Models\RuanganTransaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\Else_;

class PinActivityRuanganController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id_ruangan_transaksi)
    {
        // Menghitung jumlah pin ruangan yang sudah diinput pada waktu start_time yang sama
    
        // Menghitung jumlah pin ruangan yang sudah diinput
        $jumlah_pin = PinActivityRuangan::where('id_ruangan_transaksi', $id_ruangan_transaksi)->count();
    
        // Jika jumlah pin ruangan sudah 2 kali atau lebih, kembalikan pesan error
        if ($jumlah_pin >= 2) {
            return response()->json(['message' => 'Maaf, pin ruangannya sudah diinput lebih dari 2 kali'], 400);
        }
    
        // Ambil data dari tabel ruangan_transaksi
        $ruangan_transaksi = RuanganTransaksi::where('id_ruangan_transaksi', $id_ruangan_transaksi)->first();
    
        // Pastikan data ruangan_transaksi ditemukan
        if (!$ruangan_transaksi) {
            return response()->json(['message' => 'Data ruangan transaksi tidak ditemukan'], 404);
        }
    
        // Buat record baru di tabel pin_activity_ruangan
        $pinActivityRuangan = new PinActivityRuangan();
        $pinActivityRuangan->id_ruangan_transaksi = $ruangan_transaksi->id_ruangan_transaksi;
        $pinActivityRuangan->id_ruangan = $ruangan_transaksi->id_ruangan;
        $pinActivityRuangan->start_time = $ruangan_transaksi->start_time;
        $pinActivityRuangan->end_time = $ruangan_transaksi->end_time;
        $pinActivityRuangan->user_id = $request->input('user_id');
        $pinActivityRuangan->pin_ruangan = $ruangan_transaksi->pin_ruangan;
        $pinActivityRuangan->save();
    
        return response()->json($pinActivityRuangan, 201);
    }
    
    
    public function pinActive(Request $request, $id_ruangan, $pin_ruangan)
{
    // Cari ruangan transaksi terbaru berdasarkan id_ruangan dan pin_ruangan
    $ruangan_transaksi = RuanganTransaksi::where('id_ruangan', $id_ruangan)
                                            ->where('pin_ruangan', $pin_ruangan)
                                            ->orderBy('created_at', 'desc')
                                            ->first();

    // Pastikan ruangan transaksi ditemukan
    if (!$ruangan_transaksi) {
        return response()->json(['message' => 'Ruangan transaksi tidak ditemukan'], 404);
    }

    // Hitung jumlah pin ruangan yang sudah diaktifkan
    $jumlah_pin = PinActivityRuangan::where('id_ruangan_transaksi', $ruangan_transaksi->id_ruangan_transaksi)->count();

    // Jika jumlah pin ruangan sudah 2 kali atau lebih, kembalikan pesan error
    if ($jumlah_pin >= 2) {
        return response()->json(['message' => 'Maaf, pin ruangannya sudah diaktifkan lebih dari 2 kali'], 400);
    }

    // Buat record baru di tabel pin_activity_ruangan
    $pinActivityRuangan = new PinActivityRuangan();
    $pinActivityRuangan->id_ruangan_transaksi = $ruangan_transaksi->id_ruangan_transaksi;
    $pinActivityRuangan->id_ruangan = $ruangan_transaksi->id_ruangan;
    $pinActivityRuangan->start_time = $ruangan_transaksi->start_time;
    $pinActivityRuangan->end_time = $ruangan_transaksi->end_time;
    $pinActivityRuangan->user_id = $request->input('user_id');
    $pinActivityRuangan->pin_ruangan = $ruangan_transaksi->pin_ruangan;
    $pinActivityRuangan->save();

    return response()->json($pinActivityRuangan, 201);
}

     
    


    // // Buat record baru di tabel pin_activity_ruangan
    // $pinActivityRuangan = new PinActivityRuangan();
    // $pinActivityRuangan->id_ruangan_transaksi = $request->input('id_ruangan_transaksi');
    // $pinActivityRuangan->id_ruangan = $request->input('id_ruangan');
    // $pinActivityRuangan->start_time = $request->input('start_time');
    // $pinActivityRuangan->end_time = $request->input('end_time');
    // $pinActivityRuangan->user_id = $request->input('user_id');
    // $pinActivityRuangan->pin_ruangan = $request->input('pin_ruangan');
    // $pinActivityRuangan->save();

    // return response()->json([
    //     'message' => 'Pin activity ruangan berhasil ditambahkan',
    //     'data' => $pinActivityRuangan
    // ], 201);
    
}

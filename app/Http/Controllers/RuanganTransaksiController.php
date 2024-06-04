<?php

namespace App\Http\Controllers;

use App\Models\RuanganTransaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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


    
}

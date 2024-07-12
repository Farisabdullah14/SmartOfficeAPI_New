<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KunciRuangan;
use App\Models\Ruangan;
use App\Models\RuanganTransaksi;
use App\Models\TransaksiRuangan; // Import model TransaksiRuangan
use Illuminate\Support\Facades\Http;

class KunciRuanganController extends Controller
{
    public function KunciRuangan(Request $request, $id_ruangan, $id_user)
    {
        // Ambil data ruangan berdasarkan id_ruangan dari tabel Ruangan
        $ruangan = Ruangan::where('id_ruangan', $id_ruangan)->first();
        if (!$ruangan) {
            return response()->json(['error' => 'Ruangan tidak ditemukan'], 404);
        }

        // URL ruangan untuk dikirim ke endpoint
        $url_ruangan = $ruangan->door_lock_url;

        // Cek status terakhir dari transaksi key_ruangan untuk ruangan ini
        $lastTransaction = KunciRuangan::where('id_ruangan', $ruangan->id_ruangan)
                                        ->orderBy('created_at', 'desc')
                                        ->first();

        // Tentukan status baru berdasarkan status terakhir
        $newStatus = 'off'; // default status
        if ($lastTransaction && $lastTransaction->status === 'off') {
            $newStatus = 'on';
        }

        // Ambil transaksi terbaru dari tabel transaksi_ruangan
        $lastTransaksiRuangan = RuanganTransaksi::where('id_ruangan', $id_ruangan)
                                                ->orderBy('created_at', 'desc')
                                                ->first();
        $idTransaksiRuangan = $lastTransaksiRuangan ? $lastTransaksiRuangan->id_ruangan_transaksi : null;

        // Simpan data baru ke dalam database
        $kunciRuangan = KunciRuangan::create([
            'id_key' => $request->id_key,
            'id_ruangan' => $ruangan->id_ruangan, // Gunakan id_ruangan dari tabel Ruangan
            'url_ruangan' => $url_ruangan,
            'status' => $newStatus,
            'id_user' => $id_user,
            'id_transaksi_ruangan' => $idTransaksiRuangan,
        ]);
        // Jika berhasil, kembalikan respons sukses
        return response()->json(['message' => 'Data kunci ruangan berhasil dibuat', 'data' => $kunciRuangan], 201);
    }

    public function getStatusKunciRuangan($id_ruangan)
    {
        // Ambil data ruangan berdasarkan id_ruangan
        $ruangan = Ruangan::where('id_ruangan', $id_ruangan)->first();
        if (!$ruangan) {
            return response()->json(['error' => 'Ruangan tidak ditemukan'], 404);
        }
        // Ambil status terbaru dari tabel KunciRuangan untuk ruangan ini
        $statusTerbaru = KunciRuangan::where('id_ruangan', $ruangan->id_ruangan)
                                     ->orderBy('created_at', 'desc')
                                     ->first();
        if (!$statusTerbaru) {
            return response()->json(['error' => 'Status ruangan tidak ditemukan'], 404);
        }
        // Buat respons JSON hanya dengan id_ruangan dan status
        $responseData = [
            'id_ruangan' => $statusTerbaru->id_ruangan,
            'status' => $statusTerbaru->status,
        ];
        return $responseData;
        // return response()->json(['message' => 'Status ruangan terbaru', 'data' => $responseData], 200);
    }
}

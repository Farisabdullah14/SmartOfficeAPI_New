<?php

namespace App\Http\Controllers;

use App\Models\RuanganTransaksi;
use Illuminate\Http\Request;
use App\Models\KunciRuanganEmergency;
use App\Models\Ruangan;
use App\Models\TransaksiLampuModel;
use Illuminate\Support\Facades\Http;

class KunciRuanganEmergencyController extends Controller
{
    public function KunciRuanganEmergency(Request $request, $id_ruangan, $pin_ruangan, $id_user)
    {
        // Validasi request, jika diperlukan
        
        // Ambil data ruangan berdasarkan id_ruangan
        $ruangan = Ruangan::where('id_ruangan', $id_ruangan)->first();

        if (!$ruangan) {
            return response()->json(['message' => 'Ruangan tidak ditemukan'], 404);
        }

        // Ambil URL ruangan dari tabel ruangan
        $urlRuangan = $ruangan->door_lock_url;

        // Ambil data transaksi ruangan berdasarkan id_ruangan dan pin_ruangan
        $transaksiRuangan = RuanganTransaksi::where('id_ruangan', $id_ruangan)
                                            ->where('pin_ruangan', $pin_ruangan)
                                            ->first();

        if (!$transaksiRuangan) {
            return response()->json(['message' => 'Transaksi ruangan tidak ditemukan'], 404);
        }

          // Validasi jumlah transaksi untuk id_ruangan_transaksi
          $countTransaksi = KunciRuanganEmergency::where('id_ruangan_transaksi', $transaksiRuangan->id_ruangan_transaksi)
          ->count();

        if ($countTransaksi >= 2) {
        return response()->json(['message' => 'Transaksi untuk id_ruangan_transaksi ini sudah mencapai batas maksimal'], 400);
        }


        // Jika URL ruangan tidak kosong, kirim request ke URL tersebut
        if ($urlRuangan) {
            // try {
            //     // Kirim request ke API untuk mengubah status menjadi "On"
            //     $response = Http::post($urlRuangan . '/status', [
            //         'status' => 'On'
            //     ]);

                // Simpan transaksi ke dalam tabel kunci_ruangan_emergency
                $transaksiEmergency = KunciRuanganEmergency::create([
                    'id_ruangan' => $id_ruangan,
                    'id_ruangan_transaksi' => $transaksiRuangan->id_ruangan_transaksi,
                    'url_ruangan' => $urlRuangan,
                    'pin_ruangan' => $pin_ruangan,
                    'status' => 'On', // Sesuai dengan yang dikirimkan ke API
                    'id_user' => $id_user,
                ]);

                // Tampilkan respons atau lakukan sesuatu setelah transaksi berhasil
                return response()->json(['message' => 'Transaksi berhasil ditambahkan', 'data' => $transaksiEmergency], 201);

            // } catch (\Exception $e) {
            //     // Tangani kesalahan jika terjadi
            //     return response()->json(['message' => 'Gagal mengirim request ke URL ruangan'], 500);
            // }
        } else {
            return response()->json(['message' => 'URL ruangan kosong'], 400);
        }
    }
}

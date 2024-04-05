<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HistoryTransaksiAC;

class HistoryTransaksiAcController extends Controller
{
    public function getDataTerbaruByIdAC(Request $request, $AC_id)
{
    // Validasi request
    if (!is_string($AC_id)) {
        return response()->json(['message' => 'Parameter AC_id tidak valid'], 400);
    }


    // Ambil data terbaru berdasarkan id_AC
    $dataTerbaru = HistoryTransaksiAC::where('id_AC', $AC_id)
        ->orderByDesc('End_waktu')
        ->first();


        $selectedColumns = [
            'id_history_transaksi_AC',
            'id_Transaksi_AC',
            'id_ruangan',
            'id_AC',
            'Watt_AC',
            'Kecepatan_kipas',
            'Kode_hardware',
            'Kecepatan_Pendingin',
            'Mode',
            'Temp',
            'Time',
            'Swing',
            'Biaya_AC',
            'Start_waktu',
            'End_waktu',
            'Tarif_Listrik',
            'id_pengguna',
            'Waktu_Penggunaan',
            'Status',
        ];
        
        $ACData = $dataTerbaru->only($selectedColumns);
        return response()->json($ACData);
}



// public function getpergerakanac(Request $request, $AC_id)
// {
//     // Validasi request
//     if (!is_string($AC_id)) {
//         return response()->json(['message' => 'Parameter AC_id tidak valid'], 400);
//     }


//     // Ambil data terbaru berdasarkan id_AC
//     $dataTerbaru = HistoryTransaksiAC::where('id_AC', $AC_id)
//         ->orderByDesc('End_waktu')
//         ->first();
//     // Periksa apakah data terbaru ada

//     if (!$dataTerbaru) {
//         return response()->json(['message' => 'Data tidak ditemukan'], 404);
//     }

//         $selectedColumns = [
//             'Watt_AC',
//             'Kecepatan_kipas',
//             'Kecepatan_Pendingin',
//             'Mode',
//             'Temp',
//             'Time',
//             'Swing',
//         ];
//         $ACData = $dataTerbaru->only($selectedColumns);
//         return response()->json($ACData);
// }


public function getpergerakanac(Request $request, $AC_id)
{
    // Validasi request
    if (!is_string($AC_id)) {
        return response()->json(['message' => 'Parameter AC_id tidak valid'], 400);
    }

    // Ambil data terbaru berdasarkan id_AC dengan menggunakan metode firstOrFail()
    try {
        $dataTerbaru = HistoryTransaksiAC::where('id_AC', $AC_id)
            ->latest('End_waktu')
            ->firstOrFail();
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
        return response()->json(['message' => 'Data tidak ditemukan'], 404);
    }

    // Pilih kolom yang akan diambil
    $selectedColumns = [
        'Watt_AC',
        'Kecepatan_kipas',
        'Kecepatan_Pendingin',
        'Mode',
        'Temp',
        'Time',
        'Swing',
    ];

    // Ambil hanya data yang dipilih dari $dataTerbaru
    $ACData = $dataTerbaru->only($selectedColumns);

    // Mengembalikan response JSON dengan data AC yang dipilih
    return response()->json($ACData);
    
}


}

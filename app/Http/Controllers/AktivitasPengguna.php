<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\TransaksiLampuModel;
use App\Models\TransaksiAcModel;
use DateTime;
use App\Models\HistoryTransaksiLampu; // Tambahkan ini
use Carbon\Carbon;

class AktivitasPengguna extends Controller
{
    public function LihatDataAktivitasPengguna(Request $request, $id_pengguna)
    {
        // Ambil data transaksi lampu terbaru
        $latestTransaksiLampu = TransaksiLampuModel::where('id_pengguna', $id_pengguna)
                                                    ->where('Status', 'on')
                                                    ->orderBy('Start_waktu', 'desc') // Urutkan berdasarkan Start_waktu terlama
                                                    ->select('id_Transaksi_lampu', 'id_lampu', 'id_pengguna', 'status', 'Start_waktu')
                                                    ->get(); 
        
        // Ambil data transaksi AC terbaru
        $latestTransaksiAC = TransaksiAcModel::where('id_pengguna', $id_pengguna)
                                                ->where('Status', 'on')
                                                ->orderBy('Start_waktu', 'desc') // Urutkan berdasarkan Start_waktu terlama
                                                ->select('id_Transaksi_AC', 'id_AC', 'id_pengguna', 'status', 'Start_waktu')
                                                ->get(); 
        
        // Mengonversi data ke dalam format array
        $lampuData = $latestTransaksiLampu->toArray();
        $acData = $latestTransaksiAC->toArray();
        
        // Gabungkan data lampu dan AC
        $mergedData = array_merge($lampuData, $acData);
        
        // Urutkan array gabungan berdasarkan Start_waktu terlama
        usort($mergedData, function($a, $b) {
            if (isset($a['Start_waktu']) && isset($b['Start_waktu'])) {
                return strtotime($a['Start_waktu']) - strtotime($b['Start_waktu']);
            }
            return 0;
        });
    
        // Hapus bagian tampilan Start_waktu dari setiap elemen
        foreach ($mergedData as &$item) {
            unset($item['Start_waktu']);
        }
    
        // Format data untuk respons JSON
        return response()->json($mergedData);
    }
    

    
    
    
    
    
    
    
    
    public function JumlahPerangkatAktivitasPengguna(Request $request, $id_pengguna){
    // Ambil data transaksi lampu terbaru
    $latestTransaksiLampu = TransaksiLampuModel::where('id_pengguna', $id_pengguna)
                                                ->where('Status', 'on')
                                                ->select('id_lampu')
                                                ->distinct()
                                                ->get(); // Gunakan distinct() untuk mendapatkan perangkat yang unik
    
    // Ambil data transaksi AC terbaru
    $latestTransaksiAC = TransaksiAcModel::where('id_pengguna', $id_pengguna)
                                            ->where('Status', 'on')
                                            ->select('id_AC')
                                            ->distinct()
                                            ->get(); // Gunakan distinct() untuk mendapatkan perangkat yang unik
    
    // Menghitung jumlah perangkat yang aktif
    $jumlahPerangkatAktif = $latestTransaksiLampu->count() + $latestTransaksiAC->count();
    
    // Format data untuk respons JSON
    $responseData = [
        $jumlahPerangkatAktif
    ];

    return response()->json($responseData);

    }
    public function LihatTransaksiLampu(Request $request, $id_pengguna)
{
    // Ambil transaksi lampu
    $transaksiLampu = TransaksiLampuModel::where('id_pengguna', $id_pengguna)
                        ->where('Status', 'off')
                        ->select('id_Transaksi_lampu', 'id_lampu', 'start_waktu', 'end_waktu')
                        ->get();

    // Hitung selisih waktu dan satuan
    $transaksiLampu->transform(function ($item, $key) {
        $start = new DateTime($item->start_waktu);
        $end = new DateTime($item->end_waktu);
        $difference = $start->diff($end); // Selisih waktu dalam bentuk objek DateInterval

        // Konversi selisih waktu ke hari, bulan, atau tahun
        if ($difference->y > 0) {
            $item->selisih_waktu = $difference->y . " tahun";
        } elseif ($difference->m > 0) {
            $item->selisih_waktu = $difference->m . " bulan";
        } elseif ($difference->d > 0) {
            $item->selisih_waktu = $difference->d . " hari";
        } elseif ($difference->h > 0) {
            $item->selisih_waktu = $difference->h . " jam";
        } elseif ($difference->i > 0) {
            $item->selisih_waktu = $difference->i . " menit";
        } else {
            $item->selisih_waktu = $difference->s . " detik";
        }

        unset($item->start_waktu);
        unset($item->end_waktu);

        return $item;
    });

    return response()->json($transaksiLampu);
}

public function PenggunaanDayaLampuPerHari(Request $request, $id_pengguna)
{
    // Ambil data penggunaan daya lampu per hari dengan status Off
    $data = DB::table('transaksi_lampu')
                ->selectRaw('DATE_FORMAT(start_waktu, "%Y-%m-%d") as tanggal, id_lampu, SUM(watt_lampu) as total_watt')
                ->where('id_pengguna', $id_pengguna)
                ->where('status', 'Off')
                ->groupBy('tanggal', 'id_lampu')
                ->distinct()
                ->get();

    // Format data untuk respons JSON
    $formattedData = [];
    foreach ($data as $item) {
        $formattedData[] = [
            'tanggal' => $item->tanggal,
            'id_lampu' => $item->id_lampu,
            'total_watt' => $item->total_watt,
        ];
    }

    return response()->json($formattedData);
}
    


public function melihatDayaLampuPerHari()
{
    $today = Carbon::today();
    $sevenDaysAgo = $today->copy()->subDays(6); // Mulai dari hari ini hingga 6 hari yang lalu

    $results = TransaksiLampuModel::select(DB::raw('DATE(Start_waktu) as date'), 'id_lampu', DB::raw('MAX(Watt_lampu) as max_watt'))
        ->whereBetween('Start_waktu', [$sevenDaysAgo, $today])
        ->groupBy('date', 'id_lampu')
        ->orderBy('date', 'asc')
        ->get()
        ->groupBy('date')
        ->map(function ($day) {
            return $day->sum('max_watt');
        });

    return response()->json($results);
}

public function melihatDayaLampuPerHariPengguna(Request $request, $id_pengguna)
{
    $today = Carbon::today();
    $sevenDaysAgo = $today->copy()->subDays(6); // Mulai dari hari ini hingga 6 hari yang lalu

    $results = TransaksiLampuModel::select(DB::raw('DATE(Start_waktu) as date'), 'id_lampu', DB::raw('MAX(Watt_lampu) as max_watt'))
        ->where('id_pengguna', $id_pengguna)
        ->whereBetween('Start_waktu', [$sevenDaysAgo, $today])
        ->groupBy('date', 'id_lampu')
        ->orderBy('date', 'asc')
        ->get()
        ->groupBy('date')
        ->map(function ($day) {
            return $day->sum('max_watt');
        });

    return response()->json($results);
}

    

}

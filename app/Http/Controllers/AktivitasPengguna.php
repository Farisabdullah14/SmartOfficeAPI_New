<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\TransaksiLampuModel;
use App\Models\TransaksiAcModel;
use DateTime;
use App\Models\HistoryTransaksiLampu; // Tambahkan ini
use Carbon\Carbon;
use DateTimeZone;
use Illuminate\Support\Facades\Log;

class AktivitasPengguna extends Controller
{
//     public function LihatTransaksiLampu(Request $request, $id_pengguna)
// {
//     // Mendapatkan tanggal hari ini dan 6 hari yang lalu
//     $today = Carbon::today();
//     $sevenDaysAgo = $today->copy()->subDays(6); // Mulai dari hari ini hingga 6 hari yang lalu

//     // Ambil transaksi lampu dalam seminggu terakhir
//     $transaksiLampu = TransaksiLampuModel::where('transaksi_lampu.id_pengguna', $id_pengguna)
//         ->whereBetween('transaksi_lampu.start_waktu', [$sevenDaysAgo, $today->endOfDay()]) // Kondisi waktu dalam seminggu terakhir
//         ->where(function($query) {
//             $query->where('transaksi_lampu.Status', 'off') // Tambahkan kondisi untuk status off
//                   ->orWhere('transaksi_lampu.Status', 'on'); // Tambahkan kondisi untuk status on
//         })
//         ->select('transaksi_lampu.id_Transaksi_lampu', 'transaksi_lampu.id_lampu', 'transaksi_lampu.start_waktu', 'transaksi_lampu.end_waktu', 'ruangan.nama_ruangan', 'transaksi_lampu.watt_lampu', 'transaksi_lampu.Status')
//         ->leftJoin('ruangan', 'transaksi_lampu.id_ruangan', '=', 'ruangan.id_ruangan') // Join dengan tabel ruangan
//         ->get();

//     // Hitung selisih waktu dan satuan
//     $transaksiLampu->transform(function ($item, $key) {
//         // Jika status on, hitung selisih waktu dari start_waktu hingga waktu sekarang
//         if ($item->Status == 'on') {
//             $start = Carbon::parse($item->start_waktu, 'Asia/Jakarta'); // Waktu mulai dalam WIB (Indonesia Barat)
//             $now = Carbon::now('Asia/Jakarta'); // Waktu sekarang di WIB (Indonesia Barat)
//             $difference = $start->diff($now); // Selisih waktu dalam bentuk objek DateInterval

//             // Konversi selisih waktu ke hari, bulan, atau tahun
//             if ($difference->y > 0) {
//                 $item->selisih_waktu = $difference->y . " tahun";
//             } elseif ($difference->m > 0) {
//                 $item->selisih_waktu = $difference->m . " bulan";
//             } elseif ($difference->d > 0) {
//                 $item->selisih_waktu = $difference->d . " hari";
//             } elseif ($difference->h > 0) {
//                 $item->selisih_waktu = $difference->h . " jam";
//             } elseif ($difference->i > 0) {
//                 $item->selisih_waktu = $difference->i . " menit";
//             } else {
//                 $item->selisih_waktu = $difference->s . " detik";
//             }
            
//             // Set waktu mulai ke "-" untuk status on
//             $item->start_waktu_proto = $item->start_waktu; // Tambahkan waktu mulai asli untuk prototipe
//             $item->now_waktu_proto = $now->toDateTimeString(); // Tambahkan waktu sekarang untuk prototipe
//             $item->start_waktu = '-';
//             // Tambahkan nilai kWh "-"
//             $item->kWh = '-';
//         } else {
//             // Jika status off, hitung selisih waktu dari start_waktu hingga end_waktu
//             $start = Carbon::parse($item->start_waktu);
//             $end = Carbon::parse($item->end_waktu);
//             $difference = $start->diff($end); // Selisih waktu dalam bentuk objek DateInterval

//             // Konversi selisih waktu ke hari, bulan, atau tahun
//             if ($difference->y > 0) {
//                 $item->selisih_waktu = $difference->y . " tahun";
//             } elseif ($difference->m > 0) {
//                 $item->selisih_waktu = $difference->m . " bulan";
//             } elseif ($difference->d > 0) {
//                 $item->selisih_waktu = $difference->d . " hari";
//             } elseif ($difference->h > 0) {
//                 $item->selisih_waktu = $difference->h . " jam";
//             } elseif ($difference->i > 0) {
//                 $item->selisih_waktu = $difference->i . " menit";
//             } else {
//                 $item->selisih_waktu = $difference->s . " detik";
//             }

//             // Hitung penggunaan daya dalam kWh
//             $watt = $item->watt_lampu;
//             $hours = ($end->getTimestamp() - $start->getTimestamp()) / 3600;
//             $item->kWh = round(($watt * $hours) / 1000, 2);
//         }

//         unset($item->end_waktu);
//         unset($item->watt_lampu);
//         unset($item->start_waktu);       
//         //  unset($item->Status);


//         return $item;
//     });

//     return response()->json($transaksiLampu);
// }
public function lihatTransaksiLampuGabungan(Request $request, $id_pengguna, $periode)
{
    // Mendapatkan tanggal hari ini
    $today = Carbon::today();
    $transaksiLampu = collect(); // Inisialisasi variabel untuk menyimpan transaksi

    if ($periode === 'daily') {
        $startDate = $today->copy()->startOfDay();
        $endDate = $today->copy()->endOfDay();

        // Ambil transaksi lampu dalam hari ini
        $transaksiLampu = TransaksiLampuModel::where('transaksi_lampu.id_pengguna', $id_pengguna)
            ->whereBetween('transaksi_lampu.start_waktu', [$startDate, $endDate])
            ->where(function ($query) {
                $query->where('transaksi_lampu.Status', 'off')
                      ->orWhere('transaksi_lampu.Status', 'on');
            })
            ->select('transaksi_lampu.id_Transaksi_lampu', 'transaksi_lampu.id_lampu', 'transaksi_lampu.start_waktu', 'transaksi_lampu.end_waktu', 'ruangan.nama_ruangan', 'transaksi_lampu.watt_lampu', 'transaksi_lampu.Status')
            ->leftJoin('ruangan', 'transaksi_lampu.id_ruangan', '=', 'ruangan.id_ruangan')
            ->orderBy('transaksi_lampu.start_waktu')
            ->get();

    } elseif ($periode === 'weekly') {
        $monday = $today->copy()->startOfWeek(); // Mulai dari hari Senin minggu ini

        // Ambil transaksi lampu dalam minggu ini
        $transaksiLampu = TransaksiLampuModel::where('transaksi_lampu.id_pengguna', $id_pengguna)
            ->whereBetween('transaksi_lampu.start_waktu', [$monday, $today->endOfDay()]) // Kondisi waktu dari Senin minggu ini hingga hari ini
            ->where(function($query) {
                $query->where('transaksi_lampu.Status', 'off')
                      ->orWhere('transaksi_lampu.Status', 'on');
            })
            ->select('transaksi_lampu.id_Transaksi_lampu', 'transaksi_lampu.id_lampu', 'transaksi_lampu.start_waktu', 'transaksi_lampu.end_waktu', 'ruangan.nama_ruangan', 'transaksi_lampu.watt_lampu', 'transaksi_lampu.Status')
            ->leftJoin('ruangan', 'transaksi_lampu.id_ruangan', '=', 'ruangan.id_ruangan')
            ->orderByDesc('transaksi_lampu.start_waktu') // Mengurutkan dari yang terbaru ke yang terlama
            ->get();

    } elseif ($periode === 'monthly') {
        $startDate = $today->copy()->startOfMonth(); // Mulai dari awal bulan ini
        $endDate = $today->copy()->endOfMonth(); // Hingga akhir bulan ini

        // Ambil transaksi lampu dalam bulan ini
        $transaksiLampu = TransaksiLampuModel::where('transaksi_lampu.id_pengguna', $id_pengguna)
            ->whereBetween('transaksi_lampu.start_waktu', [$startDate, $endDate]) // Kondisi waktu dari awal bulan hingga akhir bulan ini
            ->where(function($query) {
                $query->where('transaksi_lampu.Status', 'off')
                      ->orWhere('transaksi_lampu.Status', 'on');
            })
            ->select('transaksi_lampu.id_Transaksi_lampu', 'transaksi_lampu.id_lampu', 'transaksi_lampu.start_waktu', 'transaksi_lampu.end_waktu', 'ruangan.nama_ruangan', 'transaksi_lampu.watt_lampu', 'transaksi_lampu.Status')
            ->leftJoin('ruangan', 'transaksi_lampu.id_ruangan', '=', 'ruangan.id_ruangan')
            ->orderByDesc('transaksi_lampu.start_waktu') // Mengurutkan dari yang terbaru ke yang terlama
            ->get();
    } else {
        return response()->json(['error' => 'Periode tidak valid'], 400);
    }

    // Hitung selisih waktu dan satuan
    $transaksiLampu->transform(function ($item) {
        if ($item->Status == 'on') {
            $start = Carbon::parse($item->start_waktu, 'Asia/Jakarta');
            $now = Carbon::now('Asia/Jakarta');
            $difference = $start->diff($now);

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

            $item->start_waktu_proto = $item->start_waktu;
            $item->now_waktu_proto = $now->toDateTimeString();
            $item->start_waktu = '-';
            $item->kWh = '-';
        } else {
            $start = Carbon::parse($item->start_waktu);
            $end = Carbon::parse($item->end_waktu);
            $difference = $start->diff($end);

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

            $watt = $item->watt_lampu;
            $hours = ($end->getTimestamp() - $start->getTimestamp()) / 3600;
            $item->kWh = round(($watt * $hours) / 1000, 2);
        }

        unset($item->end_waktu);
        unset($item->watt_lampu);
        return $item;
    });

    return response()->json($transaksiLampu);
}


public function lihatTransaksiLampuGabunganSemuaPengguna($periode)
{
    // Mendapatkan tanggal hari ini
    $today = Carbon::today();
    $transaksiLampu = collect(); // Inisialisasi variabel untuk menyimpan transaksi

    if ($periode === 'daily') {
        $startDate = $today->copy()->startOfDay();
        $endDate = $today->copy()->endOfDay();

        // Ambil transaksi lampu dalam hari ini
        $transaksiLampu = TransaksiLampuModel::whereBetween('transaksi_lampu.start_waktu', [$startDate, $endDate])
            ->where(function ($query) {
                $query->where('transaksi_lampu.Status', 'off')
                      ->orWhere('transaksi_lampu.Status', 'on');
            })
            ->select('transaksi_lampu.id_Transaksi_lampu', 'transaksi_lampu.id_lampu', 'transaksi_lampu.start_waktu', 'transaksi_lampu.end_waktu', 'ruangan.nama_ruangan', 'transaksi_lampu.watt_lampu', 'transaksi_lampu.Status')
            ->leftJoin('ruangan', 'transaksi_lampu.id_ruangan', '=', 'ruangan.id_ruangan')
            ->orderBy('transaksi_lampu.start_waktu')
            ->get();

    } elseif ($periode === 'weekly') {
        $monday = $today->copy()->startOfWeek(); // Mulai dari hari Senin minggu ini

        // Ambil transaksi lampu dalam minggu ini
        $transaksiLampu = TransaksiLampuModel::whereBetween('transaksi_lampu.start_waktu', [$monday, $today->endOfDay()]) // Kondisi waktu dari Senin minggu ini hingga hari ini
            ->where(function($query) {
                $query->where('transaksi_lampu.Status', 'off')
                      ->orWhere('transaksi_lampu.Status', 'on');
            })
            ->select('transaksi_lampu.id_Transaksi_lampu', 'transaksi_lampu.id_lampu', 'transaksi_lampu.start_waktu', 'transaksi_lampu.end_waktu', 'ruangan.nama_ruangan', 'transaksi_lampu.watt_lampu', 'transaksi_lampu.Status')
            ->leftJoin('ruangan', 'transaksi_lampu.id_ruangan', '=', 'ruangan.id_ruangan')
            ->orderByDesc('transaksi_lampu.start_waktu') // Mengurutkan dari yang terbaru ke yang terlama
            ->get();

    } elseif ($periode === 'monthly') {
        $startDate = $today->copy()->startOfMonth(); // Mulai dari awal bulan ini
        $endDate = $today->copy()->endOfMonth(); // Hingga akhir bulan ini

        // Ambil transaksi lampu dalam bulan ini
        $transaksiLampu = TransaksiLampuModel::whereBetween('transaksi_lampu.start_waktu', [$startDate, $endDate]) // Kondisi waktu dari awal bulan hingga akhir bulan ini
            ->where(function($query) {
                $query->where('transaksi_lampu.Status', 'off')
                      ->orWhere('transaksi_lampu.Status', 'on');
            })
            ->select('transaksi_lampu.id_Transaksi_lampu', 'transaksi_lampu.id_lampu', 'transaksi_lampu.start_waktu', 'transaksi_lampu.end_waktu', 'ruangan.nama_ruangan', 'transaksi_lampu.watt_lampu', 'transaksi_lampu.Status')
            ->leftJoin('ruangan', 'transaksi_lampu.id_ruangan', '=', 'ruangan.id_ruangan')
            ->orderByDesc('transaksi_lampu.start_waktu') // Mengurutkan dari yang terbaru ke yang terlama
            ->get();
    } else {
        return response()->json(['error' => 'Periode tidak valid'], 400);
    }

    // Hitung selisih waktu dan satuan
    $transaksiLampu->transform(function ($item) {
        if ($item->Status == 'on') {
            $start = Carbon::parse($item->start_waktu, 'Asia/Jakarta');
            $now = Carbon::now('Asia/Jakarta');
            $difference = $start->diff($now);

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

            $item->start_waktu_proto = $item->start_waktu;
            $item->now_waktu_proto = $now->toDateTimeString();
            $item->start_waktu = '-';
            $item->kWh = '-';
        } else {
            $start = Carbon::parse($item->start_waktu);
            $end = Carbon::parse($item->end_waktu);
            $difference = $start->diff($end);

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

            $watt = $item->watt_lampu;
            $hours = ($end->getTimestamp() - $start->getTimestamp()) / 3600;
            $item->kWh = round(($watt * $hours) / 1000, 2);
        }

        unset($item->end_waktu);
        unset($item->watt_lampu);
        return $item;
    });

    return response()->json($transaksiLampu);
}




public function LihatTransaksiLampu(Request $request, $id_pengguna)
{
    // Mendapatkan tanggal hari ini dan Senin minggu ini
    $today = Carbon::today();
    $monday = $today->copy()->startOfWeek(); // Mulai dari hari Senin minggu ini

    // Ambil transaksi lampu dalam minggu ini
    $transaksiLampu = TransaksiLampuModel::where('transaksi_lampu.id_pengguna', $id_pengguna)
        ->whereBetween('transaksi_lampu.start_waktu', [$monday, $today->endOfDay()]) // Kondisi waktu dari Senin minggu ini hingga hari ini
        ->where(function($query) {
            $query->where('transaksi_lampu.Status', 'off') // Tambahkan kondisi untuk status off
                  ->orWhere('transaksi_lampu.Status', 'on'); // Tambahkan kondisi untuk status on
        })
        ->select('transaksi_lampu.id_Transaksi_lampu', 'transaksi_lampu.id_lampu', 'transaksi_lampu.start_waktu', 'transaksi_lampu.end_waktu', 'ruangan.nama_ruangan', 'transaksi_lampu.watt_lampu', 'transaksi_lampu.Status')
        ->leftJoin('ruangan', 'transaksi_lampu.id_ruangan', '=', 'ruangan.id_ruangan') // Join dengan tabel ruangan
        ->orderBy('transaksi_lampu.start_waktu') // Urutkan berdasarkan start_waktu
        ->get();

    // Hitung selisih waktu dan satuan
    $transaksiLampu->transform(function ($item, $key) {
        // Jika status on, hitung selisih waktu dari start_waktu hingga waktu sekarang
        if ($item->Status == 'on') {
            $start = Carbon::parse($item->start_waktu, 'Asia/Jakarta'); // Waktu mulai dalam WIB (Indonesia Barat)
            $now = Carbon::now('Asia/Jakarta'); // Waktu sekarang di WIB (Indonesia Barat)
            $difference = $start->diff($now); // Selisih waktu dalam bentuk objek DateInterval

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
            
            // Set waktu mulai ke "-" untuk status on
            $item->start_waktu_proto = $item->start_waktu; // Tambahkan waktu mulai asli untuk prototipe
            $item->now_waktu_proto = $now->toDateTimeString(); // Tambahkan waktu sekarang untuk prototipe
            $item->start_waktu = '-';
            // Tambahkan nilai kWh "-"
            $item->kWh = '-';
        } else {
            // Jika status off, hitung selisih waktu dari start_waktu hingga end_waktu
            $start = Carbon::parse($item->start_waktu);
            $end = Carbon::parse($item->end_waktu);
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

            // Hitung penggunaan daya dalam kWh
            $watt = $item->watt_lampu;
            $hours = ($end->getTimestamp() - $start->getTimestamp()) / 3600;
            $item->kWh = round(($watt * $hours) / 1000, 2);
        }

        unset($item->end_waktu);
        unset($item->watt_lampu);
        unset($item->start_waktu);


        return $item;
    });

    // Filter data hanya untuk minggu ini dan urutkan berdasarkan hari dari Senin ke Minggu
    $filteredTransaksiLampu = $transaksiLampu->filter(function ($item) use ($monday, $today) {
        $date = Carbon::parse($item->start_waktu_proto ?? $item->start_waktu);
        return $date->between($monday, $today);
    })->values();

    return response()->json($filteredTransaksiLampu);
}

    public function aaa(){
    date_default_timezone_set('Asia/Jakarta');

    $now = Carbon::now('Asia/Jakarta'); // Waktu sekarang di WIB (Indonesia Barat)
    $aa = Carbon::now('Asia/Jakarta'); // Waktu sekarang di WIB (Indonesia Barat)
    

    // Format ulang waktu menjadi string sesuai dengan format yang diinginkan
    $formattedNow = $now->format('Y-m-d H:i:s');

    // Kemasan kedua informasi dalam sebuah array
    $response = [
        'formatted_time' => $formattedNow,
        'original_time' => $aa->toIso8601String() // Menggunakan format ISO 8601 untuk objek Carbon
    ];

    return response()->json($response);
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
        // Mendapatkan tanggal hari ini dan 6 hari yang lalu
        $today = Carbon::today();
        $sevenDaysAgo = $today->copy()->subDays(6); // Mulai dari hari ini hingga 6 hari yang lalu
    
        // Melakukan query untuk mendapatkan daya lampu maksimum per hari
        $results = TransaksiLampuModel::select(DB::raw('DATE(Start_waktu) as date'), 'id_lampu', DB::raw('MAX(Watt_lampu) as max_watt'))
            ->where('id_pengguna', $id_pengguna)
            ->whereBetween('Start_waktu', [$sevenDaysAgo, $today->endOfDay()]) // Memastikan mencakup seluruh hari ini
            ->groupBy('date', 'id_lampu')
            ->orderBy('date', 'asc')
            ->get()
            ->groupBy('date')
            ->map(function ($day) {
                return $day->sum('max_watt');
            });
    
        // Mengembalikan hasil dalam bentuk JSON
        return response()->json($results);
    }
    

    //     public function LihatDataAktivitasPengguna(Request $request, $id_pengguna)
    // {
    //     // // Ambil data transaksi lampu terbaru
    //     // $latestTransaksiLampu = TransaksiLampuModel::where('id_pengguna', $id_pengguna)
    //     //                                             ->where('Status', 'on')
    //     //                                             ->orderBy('Start_waktu', 'desc') // Urutkan berdasarkan Start_waktu terlama
    //     //                                             ->select('id_Transaksi_lampu', 'id_lampu', 'id_pengguna', 'status', 'Start_waktu')
    //     //                                             ->get(); 
        
    //     $latestTransaksiLampu = TransaksiLampuModel::where('id_pengguna', $id_pengguna)
    //                                                  ->where('Status', 'On')
    //                                                  ->orderBy('Start_waktu', 'desc')
    //                                                  ->with('lampu:id_lampu,id_ruangan') // Ambil relasi lampu dengan id_lampu dan id_ruangan saja
    //                                                  ->get(); 
        
    //     // Ambil data transaksi AC terbaru
    //     $latestTransaksiAC = TransaksiAcModel::where('id_pengguna', $id_pengguna)
    //                                             ->where('Status', 'on')
    //                                             ->orderBy('Start_waktu', 'desc') // Urutkan berdasarkan Start_waktu terlama
    //                                             ->select('id_Transaksi_AC', 'id_AC', 'id_pengguna', 'status', 'Start_waktu')
    //                                             ->get(); 
        
    //     // Mengonversi data ke dalam format array 
    //     $lampuData = $latestTransaksiLampu->toArray();
    //     $acData = $latestTransaksiAC->toArray();
        
    //     // Gabungkan data lampu dan AC
    //     $mergedData = array_merge($lampuData, $acData);
        
    //     // Urutkan array gabungan berdasarkan Start_waktu terlama
    //     usort($mergedData, function($a, $b) {
    //         if (isset($a['Start_waktu']) && isset($b['Start_waktu'])) {
    //             return strtotime($a['Start_waktu']) - strtotime($b['Start_waktu']);
    //         }
    //         return 0;
    //     });

    //     // Hapus bagian tampilan Start_waktu dari setiap elemen
    //     foreach ($mergedData as &$item) {
    //         unset($item['Start_waktu']);
    //     }

    //     // Format data untuk respons JSON
    //     return response()->json($mergedData);
    //     }   


    public function LihatDataAktivitasPengguna(Request $request, $id_pengguna)
    {
        // Ambil data transaksi lampu terbaru beserta nama ruangan
        $latestTransaksiLampu = TransaksiLampuModel::join('lampu', 'transaksi_lampu.id_lampu', '=', 'lampu.id_lampu')
                                                    ->join('ruangan', 'lampu.id_ruangan', '=', 'ruangan.id_ruangan')
                                                    ->where('transaksi_lampu.id_pengguna', $id_pengguna)
                                                    ->where('transaksi_lampu.status', 'On')
                                                    ->orderBy('transaksi_lampu.Start_waktu', 'desc')
                                                    ->select('transaksi_lampu.id_Transaksi_lampu', 'transaksi_lampu.id_lampu', 'transaksi_lampu.id_pengguna', 'transaksi_lampu.status', 'ruangan.nama_ruangan')
                                                    ->get(); 
        
        // Ambil data transaksi AC terbaru beserta nama ruangan
        $latestTransaksiAC = TransaksiAcModel::join('ruangan', 'transaksi_ac.id_ruangan', '=', 'ruangan.id_ruangan')
                                             ->where('transaksi_ac.id_pengguna', $id_pengguna)
                                             ->where('transaksi_ac.status', 'On')
                                             ->orderBy('transaksi_ac.Start_waktu', 'desc')
                                             ->select('transaksi_ac.id_Transaksi_AC', 'transaksi_ac.id_AC', 'transaksi_ac.id_pengguna', 'transaksi_ac.status', 'ruangan.nama_ruangan')
                                             ->get(); 
        
        // Menggabungkan data transaksi lampu dan AC
        $mergedData = $latestTransaksiLampu->merge($latestTransaksiAC);
        
        // Mengonversi data ke dalam format array
        $mergedDataArray = $mergedData->map(function ($item) {
            return [
                'id_Transaksi_lampu' => $item->id_Transaksi_lampu ?? null,
                'id_lampu' => $item->id_lampu ?? null,
                'id_pengguna' => $item->id_pengguna ?? null,
                'status' => $item->status ?? null,
                'nama_ruangan' => $item->nama_ruangan ?? null,
            ];
        })->toArray();
        
        // Format data untuk respons JSON
        return response()->json($mergedDataArray);
    }
    

      public function getLampuTransaksiCount(Request $request, $id_pengguna) {
            $TransaksiLampuModel = DB::table('transaksi_lampu')
                ->where('id_pengguna', $id_pengguna)
                ->where('Status', 'On')
                ->count();
        
            return $TransaksiLampuModel;
        }
        
    // public function LihatDataAktivitasSemuaPengguna(Request $request)
    // {
    //     // Ambil data transaksi lampu terbaru dengan status 'on'
    //     $latestTransaksiLampu = TransaksiLampuModel::where('Status', 'on')
    //                                                 ->orderBy('Start_waktu', 'desc') // Urutkan berdasarkan Start_waktu terlama
    //                                                 ->select('id_Transaksi_lampu', 'id_lampu', 'status', 'Start_waktu')
    //                                                 ->get(); 
        
    //     // Ambil data transaksi AC terbaru dengan status 'on'
    //     $latestTransaksiAC = TransaksiAcModel::where('Status', 'on')
    //                                             ->orderBy('Start_waktu', 'desc') // Urutkan berdasarkan Start_waktu terlama
    //                                             ->select('id_Transaksi_AC', 'id_AC', 'status', 'Start_waktu')
    //                                             ->get(); 
        
    //     // Mengonversi data ke dalam format array 
    //     $lampuData = $latestTransaksiLampu->toArray();
    //     $acData = $latestTransaksiAC->toArray();
        
    //     // Gabungkan data lampu dan AC
    //     $mergedData = array_merge($lampuData, $acData);
        
    //     // Urutkan array gabungan berdasarkan Start_waktu terlama
    //     usort($mergedData, function($a, $b) {
    //         if (isset($a['Start_waktu']) && isset($b['Start_waktu'])) {
    //             return strtotime($a['Start_waktu']) - strtotime($b['Start_waktu']);
    //         }
    //         return 0;
    //     });
    
    //     // Hapus bagian tampilan Start_waktu dari setiap elemen
    //     foreach ($mergedData as &$item) {
    //         unset($item['Start_waktu']);
    //     }
    
    //     // Format data untuk respons JSON
    //     return response()->json($mergedData);
    // }
   

    public function LihatDataAktivitasSemuaPengguna(Request $request)
    {
        // Ambil data transaksi lampu terbaru dengan status 'on' beserta nama ruangan
        $latestTransaksiLampu = TransaksiLampuModel::join('lampu', 'transaksi_lampu.id_lampu', '=', 'lampu.id_lampu')
                                                    ->join('ruangan', 'lampu.id_ruangan', '=', 'ruangan.id_ruangan')
                                                    ->where('transaksi_lampu.status', 'On')
                                                    ->orderBy('transaksi_lampu.Start_waktu', 'desc')
                                                    ->select('transaksi_lampu.id_Transaksi_lampu', 'transaksi_lampu.id_lampu', 'transaksi_lampu.status', 'ruangan.nama_ruangan')
                                                    ->get(); 
        
        // Ambil data transaksi AC terbaru dengan status 'on' beserta nama ruangan
        $latestTransaksiAC = TransaksiAcModel::join('ruangan', 'transaksi_ac.id_ruangan', '=', 'ruangan.id_ruangan')
                                             ->where('transaksi_ac.status', 'On')
                                             ->orderBy('transaksi_ac.Start_waktu', 'desc')
                                             ->select('transaksi_ac.id_Transaksi_AC', 'transaksi_ac.id_AC', 'transaksi_ac.status', 'ruangan.nama_ruangan')
                                             ->get(); 
        
        // Menggabungkan data transaksi lampu dan AC
        $mergedData = $latestTransaksiLampu->merge($latestTransaksiAC);
        
        // Mengonversi data ke dalam format array
        $mergedDataArray = $mergedData->map(function ($item) {
            return [
                'id_Transaksi_lampu' => $item->id_Transaksi_lampu ?? null,
                'id_lampu' => $item->id_lampu ?? null,
                'status' => $item->status ?? null,
                'nama_ruangan' => $item->nama_ruangan ?? null,
            ];
        })->toArray();
        
        // Format data untuk respons JSON
        return response()->json($mergedDataArray);
    }
    

    // public function hitungKwhPerHari(Request $request, $id_pengguna)
    // {
    //     // Mendapatkan tanggal hari ini dan 6 hari yang lalu
    //     $today = Carbon::today();
    //     $sevenDaysAgo = $today->copy()->subDays(6);
    
    //     // Melakukan query untuk mendapatkan data dari tabel transaksi_lampu
    //     $transactions = TransaksiLampuModel::where('id_pengguna', $id_pengguna)
    //         ->whereBetween('Start_waktu', [$sevenDaysAgo->startOfDay(), $today->endOfDay()])
    //         ->get();
    
    //     // Menghitung kWh per hari
    //     $dailyKwh = $transactions->groupBy(function($item) {
    //         return Carbon::parse($item->Start_waktu)->format('Y-m-d'); // Mengelompokkan berdasarkan tanggal
    //     })->map(function($dayTransactions) {
    //         $totalKwh = 0;
    //         foreach ($dayTransactions as $transaction) {
    //             $start = Carbon::parse($transaction->Start_waktu);
    //             $end = Carbon::parse($transaction->End_waktu);
    //             $durationInHours = $end->diffInMinutes($start) / 60;
    
    //             $watt = (float) $transaction->Watt_lampu;
    //             $kilowatt = $watt / 1000;
    //             $kwh = $kilowatt * $durationInHours;
    //             $totalKwh += $kwh;
    //         }
    //         // Membatasi angka di belakang koma menjadi lima digit
    //         return round($totalKwh, 5);
    //     });
    
    //     // Mapping hari dalam bahasa Inggris ke bahasa Indonesia
    //     $dayMapping = [
    //         'Monday' => 'Senin',
    //         'Tuesday' => 'Selasa',
    //         'Wednesday' => 'Rabu',
    //         'Thursday' => 'Kamis',
    //         'Friday' => 'Jumat',
    //         'Saturday' => 'Sabtu',
    //         'Sunday' => 'Minggu'
    //     ];
    
    //     // Membuat hasil menjadi dalam urutan hari dari Senin ke Minggu dalam bahasa Indonesia
    //     $orderedResults = collect();
    //     for ($i = 0; $i < 7; $i++) {
    //         $date = $sevenDaysAgo->copy()->addDays($i);
    //         $dayName = $dayMapping[$date->format('l')];
    //         $dateString = $date->format('Y-m-d');
    //         $orderedResults->put("$dayName ($dateString)", $dailyKwh->get($dateString, 0));
    //     }
    
    //     return response()->json($orderedResults);
    // }
    
    
    
    
    
    

    public function hitungKwhPerHari(Request $request, $id_pengguna)
    {
        // Mendapatkan tanggal hari ini dan 6 hari yang lalu
        $today = Carbon::today();
        $sevenDaysAgo = $today->copy()->subDays(6);
    
        // Melakukan query untuk mendapatkan data dari tabel transaksi_lampu
        $transactions = TransaksiLampuModel::where('id_pengguna', $id_pengguna)
            ->whereBetween('Start_waktu', [$sevenDaysAgo->startOfDay(), $today->endOfDay()])
            ->get();
    
        // Menghitung kWh per hari
        $dailyKwh = $transactions->groupBy(function($item) {
            return Carbon::parse($item->Start_waktu)->format('Y-m-d'); // Mengelompokkan berdasarkan tanggal
        })->map(function($dayTransactions) {
            $totalKwh = 0;
            foreach ($dayTransactions as $transaction) {
                $start = Carbon::parse($transaction->Start_waktu);
                $end = Carbon::parse($transaction->End_waktu);
                $durationInHours = $end->diffInMinutes($start) / 60;
    
                $watt = (float) $transaction->Watt_lampu;
                $kilowatt = $watt / 1000;
                $kwh = $kilowatt * $durationInHours;
                $totalKwh += $kwh;
            }
            // Membatasi angka di belakang koma menjadi lima digit
            return round($totalKwh, 5);
        });
    
        // Mapping hari dalam bahasa Inggris ke bahasa Indonesia
        $dayMapping = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu'
        ];
    
        // Mendapatkan tanggal Senin minggu ini
        $monday = $today->copy()->startOfWeek();
    
        // Membuat hasil menjadi dalam urutan hari dari Senin ke Minggu dalam bahasa Indonesia
        $orderedResults = collect();
        for ($i = 0; $i < 7; $i++) {
            $date = $monday->copy()->addDays($i);
            $dayName = $dayMapping[$date->format('l')];
            $dateString = $date->format('Y-m-d');
            $orderedResults->put("$dayName", $dailyKwh->get($dateString, 0));
        }
    
        return response()->json($orderedResults);
    }
    
    
        
    

public function hitungKwhPerMinggu(Request $request, $id_pengguna)
{
    // Mendapatkan tanggal hari ini dan 6 minggu yang lalu
    $today = Carbon::today();
    $sixWeeksAgo = $today->copy()->subWeeks(6); // Mulai dari hari ini hingga 6 minggu yang lalu

    // Melakukan query untuk mendapatkan data dari tabel transaksi_lampu
    $transactions = TransaksiLampuModel::where('id_pengguna', $id_pengguna)
        ->whereBetween('Start_waktu', [$sixWeeksAgo, $today->endOfDay()])
        ->get();

    // Menghitung kWh per minggu
    $weeklyKwh = $transactions->groupBy(function($item) {
        return Carbon::parse($item->Start_waktu)->weekOfYear; // Mengelompokkan berdasarkan minggu dalam setahun
    })->map(function($weekTransactions) {
        $totalKwh = 0;
        foreach ($weekTransactions as $transaction) {
            $start = Carbon::parse($transaction->Start_waktu);
            $end = Carbon::parse($transaction->End_waktu);
            $durationInHours = $end->diffInMinutes($start) / 60;

            $watt = (float) $transaction->Watt_lampu;
            $kilowatt = $watt / 1000;
            $kwh = $kilowatt * $durationInHours;
            $totalKwh += $kwh;
        }
        // Membatasi angka di belakang koma menjadi lima digit
        return round($totalKwh, 5);
    });

    // Menyiapkan array hasil dengan label minggu
    $result = [];
    for ($i = 6; $i >= 1; $i--) {
        $weekNumber = $today->copy()->subWeeks($i)->weekOfYear;
        $result['Minggu ' . (7 - $i)] = $weeklyKwh->get($weekNumber, 0);
    }

    // Mengembalikan hasil
    return response()->json($result);
}

        
public function LihatTransaksiLampuMingguan(Request $request, $id_pengguna)
{
    // Mendapatkan tanggal hari ini dan 6 minggu yang lalu
    $today = Carbon::today();
    $sixWeeksAgo = $today->copy()->subWeeks(6); // Mulai dari hari ini hingga 6 minggu yang lalu

    // Ambil transaksi lampu dalam 6 minggu terakhir
    $transaksiLampuMingguan = TransaksiLampuModel::where('transaksi_lampu.id_pengguna', $id_pengguna)
        ->whereBetween('transaksi_lampu.start_waktu', [$sixWeeksAgo, $today->endOfDay()]) // Kondisi waktu dalam 6 minggu terakhir
        ->where(function($query) {
            $query->where('transaksi_lampu.Status', 'off') // Tambahkan kondisi untuk status off
                  ->orWhere('transaksi_lampu.Status', 'on'); // Tambahkan kondisi untuk status on
        })
        ->select('transaksi_lampu.id_Transaksi_lampu', 'transaksi_lampu.id_lampu', 'transaksi_lampu.start_waktu', 'transaksi_lampu.end_waktu', 'ruangan.nama_ruangan', 'transaksi_lampu.watt_lampu', 'transaksi_lampu.Status')
        ->leftJoin('ruangan', 'transaksi_lampu.id_ruangan', '=', 'ruangan.id_ruangan') // Join dengan tabel ruangan
        ->orderByDesc('transaksi_lampu.start_waktu') // Urutkan berdasarkan data terbaru
        ->get();

    // Hitung selisih waktu dan satuan
    $transaksiLampuMingguan->transform(function ($item, $key) {
        // Jika status on, hitung selisih waktu dari start_waktu hingga waktu sekarang
        if ($item->Status == 'on') {
            $start = Carbon::parse($item->start_waktu, 'Asia/Jakarta'); // Waktu mulai dalam WIB (Indonesia Barat)
            $now = Carbon::now('Asia/Jakarta'); // Waktu sekarang di WIB (Indonesia Barat)
            $difference = $start->diff($now); // Selisih waktu dalam bentuk objek DateInterval

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
            
            // Set waktu mulai ke "-" untuk status on
            $item->start_waktu_proto = $item->start_waktu; // Tambahkan waktu mulai asli untuk prototipe
            $item->now_waktu_proto = $now->toDateTimeString(); // Tambahkan waktu sekarang untuk prototipe
            $item->start_waktu = '-';
            // Tambahkan nilai kWh "-"
            $item->kWh = '-';
        } else {
            // Jika status off, hitung selisih waktu dari start_waktu hingga end_waktu
            $start = Carbon::parse($item->start_waktu);
            $end = Carbon::parse($item->end_waktu);
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

            // Hitung penggunaan daya dalam kWh
            $watt = $item->watt_lampu;
            $hours = ($end->getTimestamp() - $start->getTimestamp()) / 3600;
            $item->kWh = round(($watt * $hours) / 1000, 2);
        }

        unset($item->end_waktu);
        unset($item->watt_lampu);
        unset($item->start_waktu);       
        //  unset($item->Status);


        return $item;
    });

    return response()->json($transaksiLampuMingguan);
}



public function hitungKwh(Request $request, $id_pengguna, $periode)
{
    $today = Carbon::today();

    if ($periode === 'daily') {
        $startDate = $today->copy()->subDays(6);
        $transactions = TransaksiLampuModel::where('id_pengguna', $id_pengguna)
            ->whereBetween('Start_waktu', [$startDate->startOfDay(), $today->endOfDay()])
            ->get();

        $kwhData = $transactions->groupBy(function($item) {
            return Carbon::parse($item->Start_waktu)->format('Y-m-d');
        })->map(function($dayTransactions) {
            $totalKwh = 0;
            foreach ($dayTransactions as $transaction) {
                $start = Carbon::parse($transaction->Start_waktu);
                $end = Carbon::parse($transaction->End_waktu);
                $durationInHours = $end->diffInMinutes($start) / 60;

                $watt = (float) $transaction->Watt_lampu;
                $kilowatt = $watt / 1000;
                $kwh = $kilowatt * $durationInHours;
                $totalKwh += $kwh;
            }
            return round($totalKwh, 5);
        });

        $dayMapping = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu'
        ];

        $monday = $today->copy()->startOfWeek();
        $orderedResults = collect();
        for ($i = 0; $i < 7; $i++) {
            $date = $monday->copy()->addDays($i);
            $dayName = $dayMapping[$date->format('l')];
            $dateString = $date->format('Y-m-d');
            $orderedResults->put("$dayName", $kwhData->get($dateString, 0));
        }

        return response()->json($orderedResults);

    }elseif ($periode === 'weekly') {
        $startDate = $today->copy()->subWeeks(4)->startOfWeek(); // Mulai dari awal minggu 4 minggu yang lalu
        $endDate = $today->endOfWeek(); // Sampai akhir minggu ini

        $weeklyData = [];
        $weekNumbers = [];

        // Loop melalui setiap minggu dari awal periode sampai akhir periode
        while ($startDate->lte($endDate)) {
            $weekStartDate = $startDate->copy();
            $weekEndDate = $startDate->copy()->endOfWeek(); // Akhir minggu

            $transactions = TransaksiLampuModel::where('id_pengguna', $id_pengguna)
                ->whereBetween('Start_waktu', [$weekStartDate->startOfDay(), $weekEndDate->endOfDay()])
                ->get();

            $weekLabel = 'Minggu ke-' . $weekStartDate->weekOfMonth;
            $weekNumbers[] = $weekLabel;

            $totalKwh = 0;
            foreach ($transactions as $transaction) {
                $start = Carbon::parse($transaction->Start_waktu);
                $end = Carbon::parse($transaction->End_waktu);
                $durationInHours = $end->diffInMinutes($start) / 60;

                $watt = (float) $transaction->Watt_lampu;
                $kilowatt = $watt / 1000;
                $kwh = $kilowatt * $durationInHours;
                $totalKwh += $kwh;
            }
            $weeklyData[] = round($totalKwh, 5);

            $startDate->addWeek(); // Pindah ke minggu berikutnya
        }

        if (empty($weeklyData)) {
            return response()->json(['error' => 'Tidak ada data untuk periode yang dipilih'], 400);
        }

        $result = array_combine($weekNumbers, $weeklyData);

        return response()->json($result);
    
        
    }elseif ($periode === 'monthly') {
        $startDate = Carbon::parse('first day of January ' . $today->year)->startOfDay();
        $endDate = Carbon::parse('last day of December ' . $today->year)->endOfDay();

        $monthlyData = [
            "Jan" => 0,
            "Feb" => 0,
            "Mar" => 0,
            "Apr" => 0,
            "May" => 0,
            "Jun" => 0,
            "Jul" => 0,
            "Aug" => 0,
            "Sep" => 0,
            "Oct" => 0,
            "Nov" => 0,
            "Dec" => 0
        ];

        while ($startDate->lte($endDate)) {
            $monthStartDate = $startDate->copy();
            $monthEndDate = $startDate->copy()->endOfMonth();

            $transactions = TransaksiLampuModel::where('id_pengguna', $id_pengguna)
                ->whereBetween('Start_waktu', [$monthStartDate->startOfDay(), $monthEndDate->endOfDay()])
                ->get();

            $monthLabel = $monthStartDate->format('M');
            $totalKwh = 0;
            foreach ($transactions as $transaction) {
                $start = Carbon::parse($transaction->Start_waktu);
                $end = Carbon::parse($transaction->End_waktu);
                $durationInHours = $end->diffInMinutes($start) / 60;

                $watt = (float) $transaction->Watt_lampu;
                $kilowatt = $watt / 1000;
                $kwh = $kilowatt * $durationInHours;
                $totalKwh += $kwh;
            }
            $monthlyData[$monthLabel] = round($totalKwh, 5);

            $startDate->addMonth();
        }

        return response()->json($monthlyData);
    }

    return response()->json(['error' => 'Periode tidak valid'], 400);
}

public function hitungKwhSemuaPengguna(Request $request, $periode)
{
    $today = Carbon::today();

    if ($periode === 'daily') {
        $startDate = $today->copy()->subDays(6);
        $transactions = TransaksiLampuModel::whereBetween('Start_waktu', [$startDate->startOfDay(), $today->endOfDay()])
            ->get();

        $kwhData = $transactions->groupBy(function($item) {
            return Carbon::parse($item->Start_waktu)->format('Y-m-d');
        })->map(function($dayTransactions) {
            $totalKwh = 0;
            foreach ($dayTransactions as $transaction) {
                $start = Carbon::parse($transaction->Start_waktu);
                $end = Carbon::parse($transaction->End_waktu);
                $durationInHours = $end->diffInMinutes($start) / 60;

                $watt = (float) $transaction->Watt_lampu;
                $kilowatt = $watt / 1000;
                $kwh = $kilowatt * $durationInHours;
                $totalKwh += $kwh;
            }
            return round($totalKwh, 5);
        });

        $dayMapping = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu'
        ];

        $monday = $today->copy()->startOfWeek();
        $orderedResults = collect();
        for ($i = 0; $i < 7; $i++) {
            $date = $monday->copy()->addDays($i);
            $dayName = $dayMapping[$date->format('l')];
            $dateString = $date->format('Y-m-d');
            $orderedResults->put("$dayName", $kwhData->get($dateString, 0));
        }

        return response()->json($orderedResults);

    } elseif ($periode === 'weekly') {
        $startDate = $today->copy()->subWeeks(4)->startOfWeek();
        $endDate = $today->endOfWeek();

        $weeklyData = [];
        $weekNumbers = [];

        while ($startDate->lte($endDate)) {
            $weekStartDate = $startDate->copy();
            $weekEndDate = $startDate->copy()->endOfWeek();

            $transactions = TransaksiLampuModel::whereBetween('Start_waktu', [$weekStartDate->startOfDay(), $weekEndDate->endOfDay()])
                ->get();

            $weekLabel = 'Minggu ke-' . $weekStartDate->weekOfMonth;
            $weekNumbers[] = $weekLabel;

            $totalKwh = 0;
            foreach ($transactions as $transaction) {
                $start = Carbon::parse($transaction->Start_waktu);
                $end = Carbon::parse($transaction->End_waktu);
                $durationInHours = $end->diffInMinutes($start) / 60;

                $watt = (float) $transaction->Watt_lampu;
                $kilowatt = $watt / 1000;
                $kwh = $kilowatt * $durationInHours;
                $totalKwh += $kwh;
            }
            $weeklyData[] = round($totalKwh, 5);

            $startDate->addWeek();
        }

        if (empty($weeklyData)) {
            return response()->json(['error' => 'Tidak ada data untuk periode yang dipilih'], 400);
        }

        $result = array_combine($weekNumbers, $weeklyData);

        return response()->json($result);

    } elseif ($periode === 'monthly') {
        $startDate = Carbon::parse('first day of January ' . $today->year)->startOfDay();
        $endDate = Carbon::parse('last day of December ' . $today->year)->endOfDay();

        $monthlyData = [
            "Jan" => 0,
            "Feb" => 0,
            "Mar" => 0,
            "Apr" => 0,
            "May" => 0,
            "Jun" => 0,
            "Jul" => 0,
            "Aug" => 0,
            "Sep" => 0,
            "Oct" => 0,
            "Nov" => 0,
            "Dec" => 0
        ];

        while ($startDate->lte($endDate)) {
            $monthStartDate = $startDate->copy();
            $monthEndDate = $startDate->copy()->endOfMonth();

            $transactions = TransaksiLampuModel::whereBetween('Start_waktu', [$monthStartDate->startOfDay(), $monthEndDate->endOfDay()])
                ->get();

            $monthLabel = $monthStartDate->format('M');
            $totalKwh = 0;
            foreach ($transactions as $transaction) {
                $start = Carbon::parse($transaction->Start_waktu);
                $end = Carbon::parse($transaction->End_waktu);
                $durationInHours = $end->diffInMinutes($start) / 60;

                $watt = (float) $transaction->Watt_lampu;
                $kilowatt = $watt / 1000;
                $kwh = $kilowatt * $durationInHours;
                $totalKwh += $kwh;
            }
            $monthlyData[$monthLabel] = round($totalKwh, 5);

            $startDate->addMonth();
        }

        return response()->json($monthlyData);
    }

    return response()->json(['error' => 'Periode tidak valid'], 400);
}

public function hitungLampu(Request $request, $id_pengguna, $periode)
{
    $today = Carbon::today();

    if ($periode === 'daily') {


        $startDate = $today->copy()->startOfDay();
        $endDate = $today->copy()->endOfDay();

        $transactions = TransaksiLampuModel::where('id_pengguna', $id_pengguna)
            ->whereBetween('Start_waktu', [$startDate, $endDate])
            ->get();

        $lampuData = $transactions->groupBy(function($item) {
            return Carbon::parse($item->Start_waktu)->format('H'); // Group by hour
        })->map(function($hourTransactions) {
            return $hourTransactions->count();
        });

        $orderedResults = collect();
        for ($hour = 0; $hour < 24; $hour++) {
            $hourLabel = str_pad($hour, 2, '0', STR_PAD_LEFT); // Format 2-digit hour
            $orderedResults->put("$hourLabel", $lampuData->get($hourLabel, 0));
        }

        return response()->json($orderedResults);



    

    } elseif ($periode === 'weekly') {



            ///////////
            $startDate = $today->copy()->subDays(6);
            $transactions = TransaksiLampuModel::where('id_pengguna', $id_pengguna)
                ->whereBetween('Start_waktu', [$startDate->startOfDay(), $today->endOfDay()])
                ->get();
    
            $lampuData = $transactions->groupBy(function($item) {
                return Carbon::parse($item->Start_waktu)->format('Y-m-d');
            })->map(function($dayTransactions) {
                return $dayTransactions->count();
            });
    
            $dayMapping = [
                'Monday' => 'Senin',
                'Tuesday' => 'Selasa',
                'Wednesday' => 'Rabu',
                'Thursday' => 'Kamis',
                'Friday' => 'Jumat',
                'Saturday' => 'Sabtu',
                'Sunday' => 'Minggu'
            ];
    
            $monday = $today->copy()->startOfWeek();
            $orderedResults = collect();
            for ($i = 0; $i < 7; $i++) {
                $date = $monday->copy()->addDays($i);
                $dayName = $dayMapping[$date->format('l')];
                $dateString = $date->format('Y-m-d');
                $orderedResults->put("$dayName", $lampuData->get($dateString, 0));
            }
    
            return response()->json($orderedResults);


       

    }  elseif ($periode === 'monthly') {
        $startDate = $today->copy()->startOfMonth()->startOfWeek();
        $endDate = $today->copy()->endOfMonth()->endOfWeek();

        $weeklyData = [];
        $weekNumbers = [];

        $weekOfMonth = 1;
        while ($startDate->lte($endDate)) {
            $weekStartDate = $startDate->copy();
            $weekEndDate = $startDate->copy()->endOfWeek();

            $transactions = TransaksiLampuModel::where('id_pengguna', $id_pengguna)
                ->whereBetween('Start_waktu', [$weekStartDate->startOfDay(), $weekEndDate->endOfDay()])
                ->get();

            $weekLabel = 'Minggu ke-' . $weekOfMonth;
            $weekNumbers[] = $weekLabel;

            $weeklyData[] = $transactions->count();

            $startDate->addWeek(); // Pindah ke minggu berikutnya
            $weekOfMonth++;
        }

        if (empty($weeklyData)) {
            return response()->json(['error' => 'Tidak ada data untuk periode yang dipilih'], 400);
        }

        $result = array_combine($weekNumbers, $weeklyData);

        return response()->json($result);

        // $startDate = Carbon::parse('first day of January ' . $today->year)->startOfDay();
        // $endDate = Carbon::parse('last day of December ' . $today->year)->endOfDay();

        // $monthlyData = [
        //     "Jan" => 0,
        //     "Feb" => 0,
        //     "Mar" => 0,
        //     "Apr" => 0,
        //     "May" => 0,
        //     "Jun" => 0,
        //     "Jul" => 0,
        //     "Aug" => 0,
        //     "Sep" => 0,
        //     "Oct" => 0,
        //     "Nov" => 0,
        //     "Dec" => 0
        // ];

        // while ($startDate->lte($endDate)) {
        //     $monthStartDate = $startDate->copy();
        //     $monthEndDate = $startDate->copy()->endOfMonth();

        //     $transactions = TransaksiLampuModel::where('id_pengguna', $id_pengguna)
        //         ->whereBetween('Start_waktu', [$monthStartDate->startOfDay(), $monthEndDate->endOfDay()])
        //         ->get();

        //     $monthLabel = $monthStartDate->format('M');
        //     $monthlyData[$monthLabel] = $transactions->count();

        //     $startDate->addMonth();
        // }

        // return response()->json($monthlyData);
    }

    return response()->json(['error' => 'Periode tidak valid'], 400);
}


public function hitungLampuSemuaPengguna(Request $request, $periode)
{
    $today = Carbon::today();

    if ($periode === 'daily') {
        $startDate = $today->copy()->startOfDay();
        $endDate = $today->copy()->endOfDay();

        $transactions = TransaksiLampuModel::whereBetween('Start_waktu', [$startDate, $endDate])->get();

        $lampuData = $transactions->groupBy(function($item) {
            return Carbon::parse($item->Start_waktu)->format('H'); // Group by hour
        })->map(function($hourTransactions) {
            return $hourTransactions->count();
        });

        $orderedResults = collect();
        for ($hour = 0; $hour < 24; $hour++) {
            $hourLabel = str_pad($hour, 2, '0', STR_PAD_LEFT); // Format 2-digit hour
            $orderedResults->put("$hourLabel", $lampuData->get($hourLabel, 0));
        }

        return response()->json($orderedResults);

    } elseif ($periode === 'weekly') {
        $startDate = $today->copy()->subDays(6);
        $transactions = TransaksiLampuModel::whereBetween('Start_waktu', [$startDate->startOfDay(), $today->endOfDay()])->get();

        $lampuData = $transactions->groupBy(function($item) {
            return Carbon::parse($item->Start_waktu)->format('Y-m-d');
        })->map(function($dayTransactions) {
            return $dayTransactions->count();
        });

        $dayMapping = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu'
        ];

        $monday = $today->copy()->startOfWeek();
        $orderedResults = collect();
        for ($i = 0; $i < 7; $i++) {
            $date = $monday->copy()->addDays($i);
            $dayName = $dayMapping[$date->format('l')];
            $dateString = $date->format('Y-m-d');
            $orderedResults->put("$dayName", $lampuData->get($dateString, 0));
        }

        return response()->json($orderedResults);

    } elseif ($periode === 'monthly') {
        $startDate = $today->copy()->subWeeks(4)->startOfWeek(); // Mulai dari awal minggu 4 minggu yang lalu
        $endDate = $today->endOfWeek(); // Sampai akhir minggu ini

        $weeklyData = [];
        $weekNumbers = [];

        // Loop melalui setiap minggu dari awal periode sampai akhir periode
        while ($startDate->lte($endDate)) {
            $weekStartDate = $startDate->copy();
            $weekEndDate = $startDate->copy()->endOfWeek(); // Akhir minggu

            $transactions = TransaksiLampuModel::whereBetween('Start_waktu', [$weekStartDate->startOfDay(), $weekEndDate->endOfDay()])->get();

            $weekLabel = 'Minggu ke-' . $weekStartDate->weekOfMonth;
            $weekNumbers[] = $weekLabel;

            $weeklyData[] = $transactions->count();

            $startDate->addWeek(); // Pindah ke minggu berikutnya
        }

        if (empty($weeklyData)) {
            return response()->json(['error' => 'Tidak ada data untuk periode yang dipilih'], 400);
        }

        $result = array_combine($weekNumbers, $weeklyData);

        return response()->json($result);
    }

    return response()->json(['error' => 'Periode tidak valid'], 400);
}



}

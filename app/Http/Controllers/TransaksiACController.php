<?php

namespace App\Http\Controllers;
use App\Models\TransaksiAcModel;
use App\Models\AC;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http; // Add this line for the Http class
use Carbon\Carbon;
use Illuminate\Support\Facades\Redis; // Import the Redis facade
use App\Http\Controllers;
use App\Models\HistoryTransaksiAC;
use Illuminate\Support\Facades\DB;

class TransaksiACController extends Controller
{
    public function getPemanggilanOn()
    {
        // Mengambil data dengan status "On"
        $transaksiOn = TransaksiAcModel::where('Status', 'On')->get();

        // Lakukan operasi lain jika diperlukan

        // Mengembalikan data dalam bentuk respons JSON atau tampilan
        return response()->json($transaksiOn);
    }

    public function showAllData()
    {
        $AC = TransaksiAcModel::all();

        if (!$AC) {
            return response()->json([
                'message' => 'Data not found',
            ], 404);
        }
        // event(new LampuStatusChanged($lampu));
        return response()->json([
            'message' => 'success',
            'data' => $AC,
        ]);
    }


    public function getMaxIdTransaksiOnStatus()
    {
        $status = 'On';
    
        $maxTransaction = TransaksiAcModel::where('Status', $status)
            ->get();
    
        if ($maxTransaction === null) {
            return response()->json([
                'message' => 'No transaction found with status "On"',
            ], 404);
        }
    
        // $maxTransactionData = TransaksiLampuModel::where('id_Transaksi_lampu', $maxTransaction)
        //     ->get();
    
        return response()->json([
            'message' => 'success',
            'data' => $maxTransaction,
        ]);
    }

    // public function getpergerakanac(Request $request, $AC_id)
    // {
    //     // Validasi request
    //     if (!is_string($AC_id)) {
    //         return response()->json(['message' => 'Parameter AC_id tidak valid'], 400);
    //     }
    
    //     // Cek apakah ada riwayat transaksi AC sebelumnya
    //     $historyTransaksi = HistoryTransaksiAC::where('id_AC', $AC_id)->exists();
    
    //     // Jika ada riwayat transaksi AC, ambil data terbaru
    //     if ($historyTransaksi) {
    //         try {
    //             $dataTerbaru = HistoryTransaksiAC::where('id_AC', $AC_id)
    //                 ->latest('End_waktu')
    //                 ->firstOrFail();
    
    //             // Pilih kolom yang akan diambil
    //             $selectedColumns = [
    //                 'Watt_AC',
    //                 'Kecepatan_kipas',
    //                 'Kecepatan_Pendingin',
    //                 'Mode',
    //                 'Temp',
    //                 'Time',
    //                 'Swing',
    //             ];
                
    
    //             // Buat transaksi AC baru dengan data dari riwayat transaksi terbaru
    //             $transaksiBaru = new TransaksiAcModel($dataTerbaru->only($selectedColumns));
    //             $transaksiBaru->save();
    
    //             // Mengembalikan response JSON dengan data AC yang dipilih
    //             return response()->json($transaksiBaru);
    //         } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
    //             return response()->json(['message' => 'Data tidak ditemukan'], 404);
    //         }
    //     } else {
    //         return response()->json(['message' => 'Tidak ada riwayat transaksi AC'], 404);
    //     }
    // }


    public function getpergerakanac(Request $request, $AC_id)
{
    $Tarif_listrika = '1444.70';

    // Validasi request
    if (!is_string($AC_id)) {
        return response()->json(['message' => 'Parameter AC_id tidak valid'], 400);
    }

    // Cek apakah ada riwayat transaksi AC sebelumnya
    $historyTransaksi = HistoryTransaksiAC::where('id_AC', $AC_id)->exists();

    // Jika ada riwayat transaksi AC, ambil data terbaru
    if ($historyTransaksi) {
        try {
            $dataTerbaru = HistoryTransaksiAC::where('id_AC', $AC_id)
                ->latest('End_waktu')
                ->firstOrFail();

                $selectedColumns = [
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
                ];
                $ACData = $dataTerbaru->only($selectedColumns);

            // Buat objek TransaksiAcModel dan isi dengan data dari riwayat transaksi terbaru
            $AC = new TransaksiAcModel;
            $AC->id_AC = $ACData['id_AC']; // Mengakses array menggunakan indeks
            $AC->id_ruangan = $ACData['id_ruangan'];
            $AC->Watt_AC = $ACData['Watt_AC'];
            $AC->Kecepatan_kipas = $ACData['Kecepatan_kipas'];
            $AC->Kecepatan_Pendingin = $ACData['Kecepatan_Pendingin'];
            $AC->Mode = $ACData['Mode'];
            $AC->Temp = $ACData['Temp'];
            $AC->Time = $ACData['Time'];
            $AC->Swing = $ACData['Swing'];
            $AC->Tarif_Listrik = $Tarif_listrika;
            $AC->id_pengguna = $request->input('id_pengguna');
            $AC->kode_hardware = $ACData['Kode_hardware'];

            // Simpan objek TransaksiAcModel
            $AC->save();

            // Lakukan logika sesuai dengan kode aslinya
            $status = 'on';
            switch ($AC->kode_hardware) {
                case "HDR_002":
                    print("oke1{$status}/{$AC->id_AC}");
                    // Lakukan operasi lain jika diperlukan
                    break;
                case "HDR_001":
                    print("oke2{$status}/{$AC->id_AC}");

                    // Lakukan operasi lain jika diperlukan
                    break;
                default:
                    return response()->json(['message' => 'Invalid Kode_hardware'], 400);
            }

            // Mengembalikan response JSON dengan data AC yang dipilih
            return response()->json($AC);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            return response()->json(['message' => 'Data tidak ditemukan di sini'], 404);
        }
    } else {
        // Periksa apakah parameter AC_id tidak diberikan
        if (!$AC_id) {
            return response()->json([
                'message' => 'Please provide id_AC parameter',
            ], 400);
        }

        // Cari data AC dengan id_AC yang diberikan
        $TBAC = AC::where('id_AC', $AC_id)->first();

        // Periksa apakah data dengan id_AC yang diberikan ada atau tidak
        if (!$TBAC) {
            return response()->json([
                'message' => 'Data not found',
            ], 404);
        }

        // Inisialisasi nilai default
        $Kecepatan_kipasa = 'Medium';
        $Kecepatan_Pendingin = 'Medium';
        $Mode = '1';
        $Temp = '1';
        $Swing = '1';
        $Time = null ;

        // Buat objek TransaksiAcModel dan isi dengan data AC yang ditemukan
        $AC = new TransaksiAcModel;
        $AC->id_AC = $TBAC->id_AC;
        $AC->id_ruangan = $TBAC->id_ruangan;
        $AC->watt_ac = $TBAC->watt_ac;
        $AC->Kecepatan_kipas = $Kecepatan_kipasa;
        $AC->Kecepatan_Pendingin = $Kecepatan_Pendingin;
        $AC->Mode = $Mode;
        $AC->Temp = $Temp;
        $AC->Time = $Time;
        $AC->Swing = $Swing;
        $AC->Tarif_Listrik = $Tarif_listrika;
        $AC->id_pengguna = $request->input('id_pengguna');
        $AC->kode_hardware = $TBAC->kode_hardware;

        // Simpan objek TransaksiAcModel
        $AC->save();

        // Lakukan logika sesuai dengan kode aslinya
        $status = 'on';
        switch ($AC->kode_hardware) {
            case "HDR_002":
                print(" oke1{$status}/{$AC->id_AC}");
                // Lakukan operasi lain jika diperlukan
                print($AC);

                break;
            case "HDR_001":
                print("oke2{$status}/{$AC->id_AC}");

                print($AC);
                // Lakukan operasi lain jika diperlukan
                break;
            default:
                return response()->json(['message' => 'Invalid Kode_hardware'], 400);
        }

        // return response()->json($AC);
    }
}

    

    public function createTransaksiAC(Request $request)
    {





        $AC = new TransaksiAcModel;
        // $AC->id_AC = $request->input('id_AC');
        $AC->id_AC = $request->input('id_AC');
        $AC->id_ruangan = $request->input('id_ruangan'); // Perbaikan 1
        // $AC->id_ruangan = $request->input('id_ruangan');
        $AC->Watt_AC = $request->input('Watt_AC');
        $AC->Kecepatan_kipas = $request->input('Kecepatan_kipas');
        $AC->Kecepatan_Pendingin = $request->input('Kecepatan_Pendingin');
        $AC->Mode = $request->input('Mode');
        $AC->Temp = $request->input('Temp');
        $AC->Time = $request->input('Time');
        $AC->Swing = $request->input('Swing');
        $AC->Tarif_Listrik = $request->input('Tarif_Listrik');
        $AC->id_pengguna = $request->input('id_pengguna');
        $AC->Waktu_Penggunaan = $request->input('Waktu_Penggunaan');
        $kode_hardware = $request->input('Kode_hardware');
        $AC->kode_hardware = $kode_hardware;

        $AC_id = $AC->id_AC; // Retrieve the ID from the saved lampu model
        $AC_ = AC::where('id_AC', $AC_id)->first();

        if (!$AC_) {
        
            return response()->json(['message' => 'AC not found'], 404);
        }
        
        $AC->save();
        $status = 'on'; // Set the desired status, assuming 'on' for this example
        // event(new LampuStatusChanged($AC->id_transaksilampu,$AC_id, $status));

        switch ($kode_hardware) {
            case "HDR_002":
                
                //  $status = 'on';
                // $endpoint = "http://192.168.100.51:8383/api/{$status}/{$lamp_id}";

                // $endpoint = "http://192.168.170.216:8383/api/{$status}/{$AC_id}";
                print(" oke1{$status}/{$AC_id}");

                // $endpoint = "http://192.168.170.216:8383/api/on/{$lamp_id}";
                $AC->save();
                // $endpoint = "http://192.168.100.51:8383/api/{$status}/{$lamp_id}";
                break;
             case "HDR_001":
                // $endpoint = "http://192.168.100.51:8383/api/{$status}/{$lamp_id}";
                // $endpoint = "http://192.168.32.76:8383/api/{$status}/{$AC_id}";
                print("oke2{$status}/{$AC_id}");
                  $AC->save();
             break;
            default:
                return response()->json(['message' => 'Invalid Kode_hardware'], 400);
        }

        // $response = Http::get($endpoint);
        // //         logger($response->body());
        // // dd($response->body());
        // if ($response->successful()) {
        //     return response()->json(['message' => 'Lamp control successful']);
        // } else {
        //     return response()->json(['error' => 'Failed to control lamp'], $response->status());
        // }

        if (!$AC) {
            return response()->json([
                'message' => 'Data not found',
            ], 404);
        }
        // return response()->json([
        //     'message' => 'success',
        //     'data' => $AC,
        // ]);
    }


    public function historyData($lampu){
    
    }


    public function cobaAmbilgetAC(Request $request, $AC_id)
{
    // Memastikan AC_id adalah string
    if (!is_string($AC_id)) {
        return response()->json(['message' => 'Parameter AC_id tidak valid'], 400);
    }

    // Menggunakan tanda kutip pada nilai parameter
    $AC = TransaksiAcModel::where('id_AC', $AC_id)
        ->where('Status', 'on')
        ->latest()
        ->first();

    if (!$AC) {
        return response()->json(['message' => 'Data tidak ditemukan'], 404);
    }

    $selectedColumns = [
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

    $ACData = $AC->only($selectedColumns);

    return response()->json($ACData);
}

    




    public function updateTransaksiAC_Kecepatan_kipas(Request $request)
    {
        // $id_Transaksi_AC = $request->input('id_Transaksi_AC');
        $id_Transaksi_AC = $request->input('id_Transaksi_AC');
        $kecepatan_kipas = $request->input('Kecepatan_kipas');
        $kode_hardware = $request->input('Kode_hardware');
        $AC = TransaksiAcModel::where('id_Transaksi_AC', $id_Transaksi_AC)->first();
    
        if (!$AC) {
            return response()->json(['error' => 'Record not found'], 404);
        }
        try {
         
    //  $status = $request->input('Status');
    //  $AC->Kecepatan_kipas = $request->input('Kecepatan_kipas');
         
    //         $kode_hardware = $request->input('Kode_hardware');
    
    //         $AC->Kode_hardware = $kode_hardware;
            // $AC->save();
            $AC->Kecepatan_kipas = $kecepatan_kipas;
            $AC->Kode_hardware = $kode_hardware;
            $AC->save();


            $status = "on";


            $AC_id = $AC->id_AC;
          //  dd($lampu);

        
        
        //   event(new LampuStatusChanged($id_Transaksi_lampu,$lamp_id, $status));
        // broadcast(new LampuStatusChanged($lampu->id_transaksilampu,$lamp_id, $status))->toOthers();

        //   event(new LampuStatusChanged('lampu_status'));

            switch ($kode_hardware) {
                case "HDR_002":
                $endpoint = "http://192.168.170.216:8383/api/{$status}/{$AC_id}";
                break;
                case "HDR_001":
                // $endpoint = "http://192.168.170.76:8383/api/{$status}/{$lamp_id}";
                $endpoint = "http://192.168.32.76:8383/api/{$status}/{$AC_id}";
                // $endpoint = "http://192.168.100.51:8383/api/{$status}/{$lamp_id}";
                    break;
                default:
                    return response()->json(['message' => 'Invalid Kode_hardware'], 400);
            }
    
            // $response = Http::get($endpoint);

            // if ($response->status() == 200) {
            //     // event(new LampuStatusChanged($lamp_id, $status)); // Emit the LampuStatusChanged event
            //     return response()->json(['message' => 'Updated successfully']);
            // } else {
            //    return response()->json(['error' => 'Failed to update'], $response->status());
            // }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}

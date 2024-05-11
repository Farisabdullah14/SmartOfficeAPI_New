<?php

namespace App\Http\Controllers;

use App\Models\LampuModel;
use App\Events\LampuStatusChanged;

use App\Http\Controllers;
use App\Models\TransaksiLampuModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http; // Add this line for the Http class
use Carbon\Carbon;
use Illuminate\Support\Facades\Redis; // Import the Redis facade
use App\Models\HistoryTransaksiLampu;
use App\Models\TarifListrik;

class TransaksiLampuController extends Controller
{
    public function getPemanggilanOn()
    {
        // Mengambil data dengan status "On"
        $transaksiOn = TransaksiLampuModel::where('Status', 'On')->get();

        // Lakukan operasi lain jika diperlukan

        // Mengembalikan data dalam bentuk respons JSON atau tampilan
        return response()->json(['transaksiOn' => $transaksiOn]);
    }
    public function showAllData()
    {
        $lampu = TransaksiLampuModel::all();

        if (!$lampu) {
            return response()->json([
                'message' => 'Data not found',
            ], 404);
        }

        // event(new LampuStatusChanged($lampu));
        return response()->json([
            'message' => 'success',
            'data' => $lampu,
        ]);
    }


    public function getMaxIdTransaksiOnStatus()
    {
        $status = 'On';
    
        $maxTransaction = TransaksiLampuModel::where('Status', $status)
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
    
    public function cobaAmbilget(Request $request, $id_lampu)
    {
            if (!is_string($id_lampu)) {
        return response()->json(['message' => 'Parameter id_lampu tidak valid'], 400);
    }
  $lampu = TransaksiLampuModel::where('id_lampu', $id_lampu)
  ->where('Status', 'on')
//  ->where('id_Transaksi_lampu', $id_Transaksi_lampu)
  ->latest()
  ->first();
  

  //$this->historyData($lampu);
//   dd($lampu->toArray());
    if (!$lampu) {
        return response()->json(['message' => 'Data tidak ditemukan'], 404);
    }

    $selectedColumns = [
        'id_Transaksi_lampu',
        'id_lampu',
        'Watt_lampu',
        'Kode_hardware',
        'Biaya_lampu',
        'id_ruangan',
        'Status',
        'id_pengguna',
    ];

    $lampuData = $lampu->only($selectedColumns);

    return response()->json($lampuData);

        // return response()->json(
        //   //  'message' => 'success',
        //      $lampu
        // );
    }
    
    public function getLatestTransactionByLampId(Request $request, $id_lampu)
    {
        // Ambil transaksi lampu terbaru berdasarkan id_lampu
        $latestTransaction = TransaksiLampuModel::where('id_lampu', $id_lampu)
            ->orderBy('created_at', 'desc')
            ->first();
        if (!$latestTransaction) {
            return response()->json(['message' => 'No transaction found for this lamp'], 404);
        }
    
        // Broadcast perubahan status lampu ke channel yang sesuai
        event(new LampuStatusChanged($latestTransaction->id_Transaksi_lampus,$latestTransaction->id_lampu, $latestTransaction->Status));
        $selectedColumns = [
            'id_Transaksi_lampu',
            'id_lampu',
            'Watt_lampu',
            'Kode_hardware',
            'Biaya_lampu',
            'Status',
            'id_pengguna',
        ];
        $lampuData = $latestTransaction->only($selectedColumns);
        return response()->json(['latest_transaction' => $lampuData]);
    }
    
    
    


    public function createTransaksiLampu(Request $request)
    {
        $latestTarif = TarifListrik::latest()->first();

        
        $Transaskilampu = new TransaksiLampuModel;
        $Transaskilampu->id_lampu = $request->input('id_lampu');
        $Transaskilampu->Watt_lampu = $request->input('Watt_lampu');
        $Transaskilampu->id_ruangan = $request->input('id_ruangan');
        $Transaskilampu->id_pengguna = $request->input('id_pengguna');
        // $Transaskilampu->id_tarif_listrik = $request->input('id_tarif_listrik'); // Changed from id_listrik to id_tarif_listrik
        // $Transaskilampu->tarif_per_kwh = $request->input('tarif_per_kwh');
              

        $Transaskilampu->id_tarif_listrik = $latestTarif->id; // Mengambil ID tarif listrik terbaru
        $Transaskilampu->tarif_per_kwh = $latestTarif->tarif_per_kwh;
    
        $kode_hardware = $request->input('Kode_hardware');
        
        $Transaskilampu->kode_hardware = $kode_hardware;

        $lamp_id = $Transaskilampu->id_lampu; // Retrieve the ID from the saved lampu model
        $lamp_ = LampuModel::where('id_lampu', $lamp_id)->first();

        if (!$lamp_) {
            return response()->json(['message' => 'Lamp not found'], 404);
        }
        
        $Transaskilampu->save();
        $status = 'on'; // Set the desired status, assuming 'on' for this example
        event(new LampuStatusChanged($Transaskilampu->id_transaksilampu,$lamp_id, $status));

        switch ($kode_hardware) {
            case "HDR_002":
                
                //  $status = 'on';
                // $endpoint = "http://192.168.100.51:8383/api/{$status}/{$lamp_id}";

                $endpoint ="0 ada" ;// "http://192.168.170.216:8383/api/{$status}/{$lamp_id}";
                // $endpoint = "http://172.20.10.5:8383/api/on/{$lamp_id}";

                // $endpoint = "http://192.168.170.216:8383/api/on/{$lamp_id}";
                // $Transaskilampu->save();

                // $endpoint = "http://192.168.100.51:8383/api/{$status}/{$lamp_id}";
                break;
             case "HDR_001":
                // http://172.20.10.5:8383/
                // $endpoint = "http://172.20.10.5:8383/api/{$status}/{$lamp_id}";
                // $endpoint = "http://172.20.10.5:8383/api/on/{$lamp_id}";

                print(" adadad  ");

             //   print($Transaskilampu->save());
                // print($endpoint = "http://172.20.10.5:8383/api/on/{$lamp_id}");
                // $endpoint = "0 ada" ; //"http:// .168.32.76:8383/api/{$status}/{$lamp_id}";
                // $Transaskilampu->save();
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
        // if (!$Transaskilampu) {
        //     return response()->json([
        //         'message' => 'Data not found',
        //     ], 404);
        // }

        // return response()->json([
        //     'message' => 'success',
        //     'data' => $lampu,
        // ]);
    }



    public function updateTransaksiLampu(Request $request)
    {
        $id_Transaksi_lampu = $request->input('id_Transaksi_lampu');
    
        $lampu = TransaksiLampuModel::where('id_Transaksi_lampu', $id_Transaksi_lampu)->first();
        if (!$lampu) {
            return response()->json(['error' => 'Record not found'], 404);
        }
    
        try {
            $status = $request->input('Status');
            $kode_hardware = $request->input('Kode_hardware');
            $lampu->id_pengguna = $request->input('id_pengguna');    
            $lampu->Status = $status;
            $lampu->Kode_hardware = $kode_hardware;
            $lampu->save();
            $lamp_id = $lampu->id_lampu;
          //  dd($lampu);

          event(new LampuStatusChanged($id_Transaksi_lampu,$lamp_id, $status));
        // broadcast(new LampuStatusChanged($lampu->id_transaksilampu,$lamp_id, $status))->toOthers();

        //   event(new LampuStatusChanged('lampu_status'));

            switch ($kode_hardware) {
                case "HDR_002":
                    $endpoint ="0 ada" ;// "http://192.168.170.216:8383/api/{$status}/{$lamp_id}";
                    print(" adadad  ");

                // $endpoint = "http://192.168.170.216:8383/api/{$status}/{$lamp_id}";
                // $endpoint = "http://192.168.170.216:8383/api/{$status}/{$lamp_id}";
                break;
                case "HDR_001":
                // $endpoint = "http://192.168.170.76:8383/api/{$status}/{$lamp_id}";
                // $endpoint = "http://192.168.32.76:8383/api/{$status}/{$lamp_id}";
                // $endpoint = "http://172.20.10.5:8383/api/{$status}/{$lamp_id}";
                $endpoint ="0 ada" ;// "http://192.168.170.216:8383/api/{$status}/{$lamp_id}";
                print(" adadad  ");

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

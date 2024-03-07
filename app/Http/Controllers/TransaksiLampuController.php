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
        'Status',
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
        ];
        $lampuData = $latestTransaction->only($selectedColumns);

        return response()->json(['latest_transaction' => $lampuData]);
    }
    
    
    


    public function createTransaksiLampu(Request $request)
    {
        $lampu = new TransaksiLampuModel;
        $lampu->id_lampu = $request->input('id_lampu');
        $lampu->Watt_lampu = $request->input('Watt_lampu');
        $lampu->id_ruangan = $request->input('id_ruangan');
        $kode_hardware = $request->input('Kode_hardware');
        $lampu->kode_hardware = $kode_hardware;

        $lamp_id = $lampu->id_lampu; // Retrieve the ID from the saved lampu model
        $lamp_ = LampuModel::where('id_lampu', $lamp_id)->first();

        if (!$lamp_) {
            return response()->json(['message' => 'Lamp not found'], 404);
        }

        $lampu->save();


        $status = 'on'; // Set the desired status, assuming 'on' for this example
       

        event(new LampuStatusChanged($lampu->id_transaksilampu,$lamp_id, $status));

        switch ($kode_hardware) {
            case "HDR_002":
                
                //  $status = 'on';
                // $endpoint = "http://192.168.100.51:8383/api/{$status}/{$lamp_id}";

                $endpoint = "http://192.168.170.216:8383/api/{$status}/{$lamp_id}";
                // $endpoint = "http://192.168.170.216:8383/api/on/{$lamp_id}";
                $lampu->save();
                // $endpoint = "http://192.168.100.51:8383/api/{$status}/{$lamp_id}";
                break;
             case "HDR_001":
                // $endpoint = "http://192.168.100.51:8383/api/{$status}/{$lamp_id}";
                $endpoint = "http://192.168.32.76:8383/api/{$status}/{$lamp_id}";
                  $lampu->save();
             break;
            default:
                return response()->json(['message' => 'Invalid Kode_hardware'], 400);
        }

        $response = Http::get($endpoint);
        //         logger($response->body());
        // dd($response->body());
        if ($response->successful()) {
            return response()->json(['message' => 'Lamp control successful']);
        } else {
            return response()->json(['error' => 'Failed to control lamp'], $response->status());
        }

        if (!$lampu) {
            return response()->json([
                'message' => 'Data not found',
            ], 404);
        }

        return response()->json([
            'message' => 'success',
            'data' => $lampu,
        ]);
    }







    public function historyData($lampu){
        


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
                $endpoint = "http://192.168.170.216:8383/api/{$status}/{$lamp_id}";
                break;
                case "HDR_001":
                // $endpoint = "http://192.168.170.76:8383/api/{$status}/{$lamp_id}";
                $endpoint = "http://192.168.32.76:8383/api/{$status}/{$lamp_id}";
                // $endpoint = "http://192.168.100.51:8383/api/{$status}/{$lamp_id}";
                    break;
                default:
                    return response()->json(['message' => 'Invalid Kode_hardware'], 400);
            }
    
            $response = Http::get($endpoint);

            if ($response->status() == 200) {
                // event(new LampuStatusChanged($lamp_id, $status)); // Emit the LampuStatusChanged event
                return response()->json(['message' => 'Updated successfully']);
            } else {
               return response()->json(['error' => 'Failed to update'], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
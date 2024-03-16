<?php

namespace App\Http\Controllers;
use App\Models\TransaksiAcModel;
use App\Models\AC;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http; // Add this line for the Http class
use Carbon\Carbon;
use Illuminate\Support\Facades\Redis; // Import the Redis facade
use App\Http\Controllers;

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

    public function createTransaksiAC(Request $request)
    {
        $AC = new TransaksiAcModel;
        // $AC->id_AC = $request->input('id_AC');
        $AC->id_AC = $request->input('id_AC');

        $AC->id_ruangan = $request->input('id_ruangan');
        $AC->Watt_AC = $request->input('Watt_AC');
        $AC->Kecepatan_kipas = $request->input('Kecepatan_kipas');
        $AC->Kecepatan_Pendingin = $request->input('Kecepatan_Pendingin');
        $AC->Mode = $request->input('Mode');
        $AC->Temp = $request->input('Temp');
        $AC->Time = $request->input('Time');
        $AC->Swing = $request->input('Swing');
        $kode_hardware = $request->input('Kode_hardware');
        $AC->kode_hardware = $kode_hardware;

        $AC_id = $AC->id_AC; // Retrieve the ID from the saved lampu model
        $AC_ = AC::where('id_AC', $AC_id)->first();

        if (!$AC_) {
        
            return response()->json(['message' => 'Lamp not found'], 404);
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

<?php

// app/Http/Controllers/LampuController.php

namespace App\Http\Controllers;
use App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LampuModel;
use App\Models\TransaksiLampuModel;

class LampuController extends Controller
{
    public function showAllData()
    {
        $lampu = LampuModel::all();
        if (!$lampu) {
            return response()->json([
                'message' => 'Data not found',
            ], 404);
        }

        return response()->json(
             $lampu,
        );
     }

     public function showSelectedData()
{
    $lampu = LampuModel::select('jenis_lampu', 'watt_lampu')->get();

    if ($lampu->isEmpty()) {
        return response()->json([
            'message' => 'Data not found',
        ], 404);
    }

    return response()->json($lampu);
}
            //  $lampu->pluck('jenis_lampu'),
    //  'id_lampu' => $lampu->id_lampu,
    //  'jenis_lampu' =>$lampu->jenis_lampu,
    //  'watt_lampu' =>$lampu ->watt_lampu,

     public function createTransaksiLampu(Request $request)
    {
        $lampu = new TransaksiLampuModel;
        $lampu->jenis_lampu = $request->input('jenis_lampu');
        $lampu->watt_lampu	 = $request->input('watt_lampu');
        $lampu->save();
        
        if (!$lampu) {
            return response()->json([
                'message' => 'Data not ',
            ], 404);
        }

        return response()->json([
            'message' => 'success',
            'data' => $lampu->jenis_lampu,
        ]);
    }
}
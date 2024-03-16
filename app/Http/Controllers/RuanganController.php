<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use App\Models\Ruangan;
class RuanganController extends Controller
{
    public function ALLDataRuangan()
    {
        // $ruangan = Ruangan::all();
        // return response()->json($ruangan);
      
        
        $ruangan = Ruangan::select('id_ruangan', 'nama_ruangan','status')->get();
        return response()->json($ruangan);
    }

    public function getDataByIdRuangan($idRuangan)
{
    $lampuData = DB::table('Lampu')
                    ->select('id_lampu', 'jenis_lampu', 'watt_lampu')
                    ->where('id_ruangan', $idRuangan)
                    ->get();

    $acData = DB::table('ac')
                ->select('id_AC', 'jenis_ac', 'watt_ac', 'daya_va', 'paard_kracht')
                ->where('id_ruangan', $idRuangan)
                ->get();

    $mergedData = array_merge($lampuData->toArray(), $acData->toArray());

    return response()->json($mergedData);
}

}

// return response()->json([
//     'perangkatList' => [
//         $lampuData,
//         $acData
//     ]
// ]);
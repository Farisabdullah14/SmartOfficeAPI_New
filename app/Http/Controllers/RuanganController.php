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
        // $ruangan = Ruangan::all();
        // return response()->json($ruangan);
      
        $lampuData = DB::table('Lampu')
                        ->where('id_ruangan', $idRuangan)
                        ->get();

        // Mengambil data dari tabel AC berdasarkan id_ruangan
        $acData = DB::table('ac')
                    ->where('id_ruangan', $idRuangan)
                    ->get();

        // Mengembalikan response dalam bentuk JSON
        // return response()->json([
        //     'lampu_data' => $lampuData,
        //     'ac_data' => $acData
        // ]);
        return response()->json([
            $lampuData,
            $acData
        ]);
    }
}

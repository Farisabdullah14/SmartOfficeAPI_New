<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AC;
class ACController extends Controller
{
    public function showAllData()
    {
        $AC = AC::all();
        if (!$AC) {
            return response()->json([
                'message' => 'Data not found',
            ], 404);
        }

        return response()->json(
             $AC,
        );
     }

      public function searchDataByIdAC($idAC)
    {
        $AC = AC::where('id_AC', $idAC)->get();
        if ($AC->isEmpty()) {
            return response()->json([
                'message' => 'Data not found',
            ], 404);
        }

        return response()->json($AC);
    }
     
     
}

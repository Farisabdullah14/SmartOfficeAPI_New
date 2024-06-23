<?php

// app/Http/Controllers/LampuController.php

namespace App\Http\Controllers;
use App\Http\Controllers;
use Illuminate\Support\Facades\Log;

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

    public function createLampu(Request $request)
    {
        $request->validate([
            'jenis_lampu' => 'required|string|max:255',
            'watt_lampu' => 'required|integer',
            'Kode_hardware' => 'required|string|max:255',
            'id_ruangan' => 'required|string|max:255', // tambahkan validasi untuk id_ruangan
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
    
        $lampu = new LampuModel;
        $lampu->jenis_lampu = $request->input('jenis_lampu');
        $lampu->watt_lampu = $request->input('watt_lampu');
        $lampu->Kode_hardware = $request->input('Kode_hardware');
        $lampu->id_ruangan = $request->input('id_ruangan'); // tambahkan id_ruangan
    
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images', 'public');
            $lampu->image = $imagePath;
        }
    
        $lampu->save();
    
        if (!$lampu) {
            Log::error('Failed to save lampu', ['lampu' => $lampu]);
            return response()->json([
                'message' => 'Data not saved',
            ], 404);
        }
    
        Log::info('Lampu created successfully', ['lampu' => $lampu]);
    
        return response()->json([
            'message' => 'success',
            'data' => $lampu,
        ]);
    }

    public function updateLampu(Request $request, $id_lampu)
    {
        $request->validate([
            'keterangan' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $lampu = LampuModel::where('id_lampu', $id_lampu)->first();
        
        if (!$lampu) {
            return response()->json([
                'message' => 'Lamp not found',
            ], 404);
        }

        if ($request->has('keterangan')) {
            $lampu->keterangan = $request->input('keterangan');
        }

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images', 'public');
            $lampu->image = $imagePath;
        }

        $lampu->save();

        return response()->json([
            'message' => 'Lamp updated successfully',
            'data' => $lampu,
        ]);
    }

    public function getImageAndKeterangan($id_lampu)
    {
        $lampu = LampuModel::where('id_lampu', $id_lampu)->first();
    
        if (!$lampu) {
            Log::error('Lamp not found', ['id_lampu' => $id_lampu]);
            return response()->json([
                'message' => 'Lamp not found',
            ], 404);
        }
    
        $data = [
            'keterangan' => $lampu->keterangan,
            'image_url' => $lampu->image ? asset('storage/' . $lampu->image) : null,
        ];
    
        Log::info('Lamp details retrieved successfully', ['data' => $data]);
    
        return response()->json($data);
    }
    }
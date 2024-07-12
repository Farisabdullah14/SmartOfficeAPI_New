<?php

// app/Http/Controllers/LampuController.php

namespace App\Http\Controllers;
use App\Http\Controllers;
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;
use App\Models\LampuModel;
use App\Models\Ruangan;
use App\Models\HistoryTransaksiLampu;
use App\Models\TarifListrik;
use App\Models\PcdMasterUser;
use App\Models\TransaksiLampuModel;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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





    public function getLampuDetail(Request $request, $id_transaksi_lampu)
{
    // Ambil data transaksi lampu berdasarkan id_transaksi_lampu
    $transaksiLampu = TransaksiLampuModel::where('id_transaksi_lampu', $id_transaksi_lampu)->first();

    if (!$transaksiLampu) {
        return response()->json(['message' => 'Transaksi lampu tidak ditemukan'], 404);
    }

    // Ambil data lampu
    $lampu = LampuModel::where('id_lampu', $transaksiLampu->id_lampu)->first();

    // Ambil data ruangan
    $ruangan = Ruangan::where('id_ruangan', $transaksiLampu->id_ruangan)->first();

    // Ambil data tarif listrik
    $tarifListrik = TarifListrik::where('id', $transaksiLampu->id_tarif_listrik)->first();

    // Ambil history transaksi lampu
    $historyTransaksiLampuOn = HistoryTransaksiLampu::where('id_transaksi_lampu', $id_transaksi_lampu)
        ->where('status', 'On')
        ->first();

    $historyTransaksiLampuOff = HistoryTransaksiLampu::where('id_transaksi_lampu', $id_transaksi_lampu)
        ->where('status', 'Off')
        ->first();

    // Ambil nama pengguna
    $userOn = $historyTransaksiLampuOn ? PcdMasterUser::where('id', $historyTransaksiLampuOn->id_pengguna)->first() : null;
    $userOff = $historyTransaksiLampuOff ? PcdMasterUser::where('id', $historyTransaksiLampuOff->id_pengguna)->first() : null;


    // Hitung total biaya lampu
    $totalBiayaLampu = $transaksiLampu->Biaya_lampu;
    // $Watt = $transaksiLampu->Watt_lampu;

    // Parsing waktu dari string menjadi objek Carbon
    $startOnWaktu = Carbon::parse($transaksiLampu->Start_waktu);
    $startOnTime = $startOnWaktu->format('H:i:s');

    $endOffWaktu = Carbon::parse($transaksiLampu->End_waktu);
    $endOffTime = $endOffWaktu->format('H:i:s');

    // Hitung Kwh yang digunakan
    $start = Carbon::parse($transaksiLampu->Start_waktu);
    $end = Carbon::parse($transaksiLampu->End_waktu);
    $durationInHours = $end->diffInMinutes($start) / 60;

    $watt = (float) $transaksiLampu->Watt_lampu;
    $kilowatt = $watt / 1000;
    $kwh = $kilowatt * $durationInHours;

    // Mendapatkan nama hari dalam Bahasa Indonesia
    $hari = $startOnWaktu->translatedFormat('l'); // 'l' menghasilkan nama hari dalam Bahasa Indonesia

    $data = [
        'id_lampu' => $lampu->id_lampu,
        'nama_ruangan' => $ruangan->nama_ruangan,
        'status_lampu_terbaru' => $transaksiLampu->Status,
        'tarif_listrik' => 'Rp.' . $tarifListrik->tarif_per_kwh,
        'user_on' => $userOn ? $userOn->name : null,
        'user_off' => $userOff ? $userOff->name : null,
        'total_biaya_lampu' => 'Rp.' . number_format($totalBiayaLampu, 2, ',', '.'),
        'kwh_digunakan' => $kwh,
        'Watt' => $watt,
        'on_waktu' => $startOnTime,
        'off_waktu' => $endOffTime,
        'hari' => $hari,
    ];

    return response()->json($data);
}

    
    
    

    }
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RuanganTransaksi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CheckRoomTransactionEndTime extends Command
{
    protected $signature = 'check:room-endtime';
    protected $description = 'Check room transactions end time and turn off door lock if needed';

    public function __construct()
    {
        parent::__construct();
    }


// public function handle2()
// {
//     Log::info('Checking room transactions end time.');

//     $currentTime = Carbon::now();

//     // dd("currentTime" . $currentTime);

//     // Dapatkan semua transaksi yang sudah berakhir
//     $expiredTransactions = RuanganTransaksi::where('end_time', '<=', $currentTime)
//                                             ->where('status', '!=', 'off')
//                                             ->get();

//     foreach ($expiredTransactions as $transaction) {
//         Log::info("Processing transaction ID: {$transaction->id_ruangan_transaksi}");
        
//         dd("hlo   ". $ruangan);

//         // Cari ruangan terkait
//         $ruangan = $transaction->ruangan;

//         if ($ruangan) {

//             // Trigger ke hardware door lock untuk menonaktifkan
//             // $response = Http::get("{$ruangan->door_lock_url}/API/{$transaction->id_ruangan}/Off");
// // dd("halo  " +  $response);
//             // if ($response->successful()) {
//                 // Update status transaksi ke 'off'
//                 $transaction->status = 'off';
//                 $transaction->save();

//                 Log::info("Door lock for room {$transaction->id_ruangan} has been turned off.");
//             // } else {
//             //     Log::error("Failed to turn off door lock for room {$transaction->id_ruangan}.");
//             // }
//         } else {
//         // dd("hlo   ". $ruangan);

//             Log::warning("Invalid or missing door lock URL for room {$transaction->id_ruangan}.");
//         }
//     }

//     return ;
// }

// public function handle3()
// {
//     Log::info('Checking room transactions end time.');

//     $currentTime = Carbon::now();

//     // Dapatkan semua transaksi yang sudah berakhir
//     $expiredTransactions = RuanganTransaksi::where('end_time', '<=', $currentTime)
//                                             ->where('status', '!=', 'off')
//                                             ->get();

//     foreach ($expiredTransactions as $transaction) {
//         Log::info("Processing transaction ID: {$transaction->id_ruangan_transaksi}");

//         // Update status transaksi ke 'off'
//         $transaction->status = 'off';
//         $transaction->save();

//         return 
//         // $transaction = Http::get("{$ruangan->door_lock_url}/API/{$transaction->id_ruangan}/Off");

//         Log::info("Status for transaction ID {$transaction->id_ruangan_transaksi} has been set to 'off'.");
//     }

//     Log::info('Room transactions end time checking complete.');

//     return;
// }


public function handle()
{
    Log::info('Checking room transactions end time.');

    $currentTime = Carbon::now();
    $futureTime = $currentTime->copy()->addMinute(); // Adjust this value if you want a different time window

    Log::info('Current time: ' . $currentTime);
    Log::info('Future time: ' . $futureTime);

    // Dapatkan semua transaksi yang mendekati end_time
    $nearEndTransactions = RuanganTransaksi::where('end_time', '>', $currentTime)
                                            ->where('end_time', '<=', $futureTime)
                                            ->where('status', '!=', 'off')
                                            ->get();

    // dd("hhhhh  ".$nearEndTransactions);
    Log::info('Transactions found: ' . $nearEndTransactions->count());
    Log::debug('Transactions details: ', $nearEndTransactions->toArray());

    if ($nearEndTransactions->isEmpty()) {
        Log::info('No transactions near end time.');
        return;
    }

    foreach ($nearEndTransactions as $transaction) {
        Log::info("Processing transaction ID: {$transaction->id_ruangan_transaksi}");

        // Update status transaksi ke 'off'
        $transaction->status = 'off';
        $transaction->save();

        Log::info("Status for transaction ID {$transaction->id_ruangan_transaksi} has been set to 'off'.");

        // Cari ruangan terkait
        $ruangan = $transaction->ruangan;
        if ($ruangan && $ruangan->door_lock_url) {
            // Trigger ke hardware door lock untuk menonaktifkan
            $response = Http::get("{$ruangan->door_lock_url}/API/{$transaction->id_ruangan_transaksi}/Off");

            if ($response->successful()) {
                Log::info("Door lock for room {$transaction->id_ruangan_transaksi} has been turned off.");
            } else {
                Log::error("Failed to turn off door lock for room {$transaction->id_ruangan_transaksi}.");
            }
        } else {
            Log::warning("Invalid or missing door lock URL for room {$transaction->id_ruangan_transaksi}.");
        }
    }

    Log::info('Room transactions end time checking complete.');

    return;
}



}

<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\TransaksiLampuModel;
use Carbon\Carbon;

use Illuminate\Console\Command;

class CheckPhase2LampTransactionEndTime extends Command
{
          /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transaksiPhase2:cek-lampu';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menonaktifkan transaksi lampu yang masih on pada jam 6 sore WIB';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $currentTime = Carbon::now('Asia/Jakarta')->format('H:i');
        
        // Cek apakah waktu sekarang adalah jam 21:00 WIB
        if ($currentTime === '21:00') {
            Log::info('Checking lamp transactions end time at 6 PM WIB.');

            // Dapatkan semua transaksi lampu yang masih aktif
            $activeLampTransactions = TransaksiLampuModel::where('status', '!=', 'off')->get();

            foreach ($activeLampTransactions as $transaction) {
                Log::info("Processing transaction ID: {$transaction->id_Transaksi_lampu}");

                // Update status transaksi menjadi 'off'
                $transaction->status = 'off';
                $transaction->save();

                Log::info("Status for transaction ID {$transaction->id_Transaksi_lampu} has been set to 'off'.");

                // Cari informasi lampu terkait
                $lampu = $transaction->lampu; // pastikan ada relasi yang benar antara TransaksiLampu dan Lampu

                if ($lampu) {
                    // Trigger ke hardware lampu untuk menonaktifkan
                    $endpoint = "http://192.168.100.160:8181/api/Off/{$lampu->id_lampu}";
                    $response = Http::get($endpoint);

                    if ($response->successful()) {
                        Log::info("Lampu {$lampu->id_lampu} has been turned off.");
                    } else {
                        Log::error("Failed to turn off lampu {$lampu->id_lampu}.");
                    }
                } else {
                    Log::warning("Invalid or missing hardware code for lampu {$lampu->id_lampu}.");
                }
            }

            Log::info('Lamp transactions end time checking complete.');
        }

        return 0;
    }
}

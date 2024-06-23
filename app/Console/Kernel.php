<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Menjalankan command check:room-endtime setiap menit
        $schedule->call(function () {
            // Logika untuk memeriksa transaksi ruangan
            \Log::info('Checking room transactions end time from Kernel schedule.');

            $currentTime = now();
            $endTimeThreshold = $currentTime->copy()->addMinutes(5);

            // Dapatkan semua transaksi yang akan berakhir dalam 5 menit ke depan
            $nearExpiredTransactions = \App\Models\RuanganTransaksi::where('end_time', '<=', $endTimeThreshold)
                                                                   ->where('status', '!=', 'off')
                                                                   ->get();

            foreach ($nearExpiredTransactions as $transaction) {
                \Log::info("Processing transaction ID: {$transaction->id_ruangan_transaksi}");

                // Update status transaksi ke 'off'
                $transaction->status = 'off';
                $transaction->save();

                \Log::info("Status for transaction ID {$transaction->id_ruangan_transaksi} has been set to 'off'.");

                // Cari ruangan terkait
                $ruangan = $transaction->ruangan;

                if ($ruangan && $ruangan->door_lock_url) {
                    // Trigger ke hardware door lock untuk menonaktifkan
                    $response = \Illuminate\Support\Facades\Http::get("{$ruangan->door_lock_url}/API/{$transaction->id_ruangan_transaksi}/Off");

                    if ($response->successful()) {
                        \Log::info("Door lock for room {$transaction->id_ruangan_transaksi} has been turned off.");
                    } else {
                        \Log::error("Failed to turn off door lock for room {$transaction->id_ruangan_transaksi}.");
                    }
                } else {
                    \Log::warning("Invalid or missing door lock URL for room {$transaction->id_ruangan_transaksi}.");
                }
            }

            \Log::info('Room transactions end time checking from Kernel schedule complete.');
        })->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

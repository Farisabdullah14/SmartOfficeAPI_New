<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RuanganTransaksi;
use App\Models\PinActivityRuangan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;

class CheckRoomBookingTime extends Command
{
    protected $signature = 'check:room-booking-time';
    protected $description = 'Check room booking time and send notification if less than 10 minutes remaining';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $currentTime = Carbon::now();
        $endTimeThreshold = $currentTime->copy()->addMinutes(10); // Waktu batas akhir adalah 10 menit dari sekarang

        $nearEndBookings = RuanganTransaksi::where('end_time', '>', $currentTime)
            ->where('end_time', '<=', $endTimeThreshold)
            ->where('status', '!=', 'off')
            ->get();

        foreach ($nearEndBookings as $booking) {
            // Cek apakah ada aktivitas PIN terkait dengan transaksi ini
            $pinActivity = PinActivityRuangan::where('id_ruangan_transaksi', $booking->id_ruangan_transaksi)
                ->where('start_time', '<=', $currentTime)
                ->where('end_time', '>=', $currentTime)
                ->where('user_id', $booking->user_id)
                ->first();

            if ($pinActivity) {
                // Kirim notifikasi ke Android
                $message = "Waktu tersisa 10 menit untuk keluar meeting, jika anda tidak keluar maka pintu akan ditutup otomatis.";
                $this->sendNotificationToAndroid($pinActivity->id_ruangan, $message);
            }
        }

        $this->info('Room booking time check completed.');
    }

    private function sendNotificationToAndroid($roomId, $message)
{
    $client = new Client();
    try {
        $response = $client->post('http://192.168.100.229:8181/api/', [
            'json' => [
                'roomId' => $roomId,
                'message' => $message
            ]
        ]);

        // Cek status response, misalnya jika berhasil atau tidak
        if ($response->getStatusCode() == 200) {
            $this->info("Notification sent successfully for room ID: $roomId - Message: $message");
        } else {
            $this->error("Failed to send notification for room ID: $roomId - Message: $message");
        }
    } catch (\Exception $e) {
        $this->error("Error sending notification: " . $e->getMessage());
    }
}

    // private function sendNotificationToAndroid($roomId, $message)
    // {
        
    //     // Lakukan logika untuk mengirim notifikasi ke Android di sini
    //     // Contoh: menggunakan HTTP request atau layanan notifikasi yang tersedia
    //     // Misalnya, Http::post('url_notifikasi_android', ['roomId' => $roomId, 'message' => $message]);
    //     // Untuk contoh ini, kita akan hanya mencatat di log
    //     $this->info("Notification sent for room ID: $roomId - Message: $message");
    // }
}


<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;



class LampuStatusChanged implements ShouldBroadcast
{
    // use Dispatchable, InteractsWithSockets, SerializesModels;

    public $lampId; 
    public $status;
    public $id_Transaksi_lampu;

    // public function __construct($lampId)
    public function __construct($id_Transaksi_lampu,$lampId, $status )
    {  
        $this->id_Transaksi_lampu = $id_Transaksi_lampu;
        $this->lampId = $lampId;
        $this->status = $status;
    }

    public function broadcastOn()
    {
        return new Channel("lampu-status");
    }   

    public function broadcastAs()
  {
      return 'real-led';
  }
}
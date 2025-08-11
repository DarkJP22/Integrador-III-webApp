<?php

namespace App\Events;

use App\Orders;
use App\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PharmacyOrderUpdate implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public User $transmitter, public Orders $order)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('App.User.'.$this->order->user_id)
            //new PrivateChannel('order.'.$this->order->id.'.'.$this->order->user_id),
        ];
    }
}

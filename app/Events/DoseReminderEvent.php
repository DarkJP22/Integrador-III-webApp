<?php

namespace App\Events;

use App\Dosereminder;
use App\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DoseReminderEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public User $receptor, public Dosereminder $dosereminder)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('App.User.'.$this->receptor->id)
        ];
    }
}

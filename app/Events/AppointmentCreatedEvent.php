<?php

namespace App\Events;


use App\Appointment;
use App\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AppointmentCreatedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public User $transmitter, public Appointment $appointment)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('App.User.'.$this->appointment->user_id)
        ];
    }
}

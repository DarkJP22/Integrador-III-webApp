<?php

namespace App\Events;

use App\AppointmentRequest;
use App\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AppointmentRequestCreatedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;



    public function __construct(public User $transmitter, public AppointmentRequest $appointmentRequest)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('App.User.'.$this->appointmentRequest->medic_id),
        ];
    }
}

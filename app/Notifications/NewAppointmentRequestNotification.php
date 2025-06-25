<?php

namespace App\Notifications;

use App\AppointmentRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;

class NewAppointmentRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public AppointmentRequest $appointmentRequest, public $callback = null)
    {
    }

    public function via($notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage([

            "data" => [
                'message' => "Nueva solicitud de Cita ". $this->appointmentRequest->date,
                "link" => $this->callback ? $this->callback : "/appointment-requests"
            ]

        ]);
    }

    public function toArray($notifiable): array
    {
        return [
            "message" => "Nueva solicitud de Cita " . $this->appointmentRequest->date,
            "link" => $this->callback ? $this->callback : "/appointment-requests",
            "title" => "Solicitud de Cita",
            "body" => "El paciente ". Optional($this->appointmentRequest->patient)->fullname." ha solicitado una cita el " . Carbon::parse($this->appointmentRequest->start)->format('Y-m-d h:i:s A')
        ];
    }
}

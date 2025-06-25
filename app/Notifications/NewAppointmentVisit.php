<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use App\Appointment;
use App\LabAppointmentRequest;
use Illuminate\Support\Carbon;

class NewAppointmentVisit extends Notification implements ShouldQueue
{
    use Queueable;

    public $appointment;
    public $callback;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(LabAppointmentRequest $appointment, $callback = null)
    {
        $this->appointment = $appointment;
        $this->callback = $callback;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail','database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                ->subject('Nueva Solicitud de visita')        
                ->markdown('mail.appointmentRequest.new', ['appointment' => $this->appointment]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            "message" => "Visita solicitada  " . $this->appointment->created_at,
            "link" => $this->callback ? $this->callback : "/agenda",
            "title" => "Cita reservada",
            "body" => "El paciente ". Optional($this->appointment->patient)->fullname." ha solicitado una visita en " . $this->appointment->visit_location
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([

            "data" => [
                'message' => "Visita solicitada ". $this->appointment->created_at,
                "link" => $this->callback ? $this->callback : "/agenda?date=" . $this->appointment->created_at->ToDateString()
            ]
            
        ]);
    }
}

<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use App\AppointmentRequest;
use Illuminate\Support\Carbon;

class ConfirmationAppointmentRequest extends Notification implements shouldQueue
{
    use Queueable;

    public $appointmentRequest;
    public $callback;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(AppointmentRequest $appointmentRequest, $callback = null)
    {
        $this->appointmentRequest = $appointmentRequest;
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
        return ['database', 'mail'];
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
                ->subject('Confirmación de solicitud Cita')        
                ->markdown('mail.appointmentRequest.confirmation', ['appointment' => $this->appointmentRequest]);
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
            "message" => "Confirmación de solicitud Cita " . $this->appointmentRequest->scheduled_date,
            "link" => $this->callback ? $this->callback : "/agenda",
            "title" => "Cita confirmada",
            "body" => "El paciente ". Optional($this->appointmentRequest->patient)->fullname." ha reservado una cita para el " . Carbon::parse($this->appointmentRequest->start)->format('Y-m-d h:i:s A')
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([

            "data" => [
                'message' => "Cita reservada ". $this->appointmentRequest->start,
                "link" => $this->callback ? $this->callback : "/agenda?date=" . $this->appointmentRequest->date->ToDateString()
            ]
            
        ]);
    }
}

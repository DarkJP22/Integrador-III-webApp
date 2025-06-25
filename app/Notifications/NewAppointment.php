<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use App\Appointment;
use Illuminate\Support\Carbon;

class NewAppointment extends Notification implements ShouldQueue
{
    use Queueable;

    public $appointment;
    public $callback;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Appointment $appointment, $callback = null)
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
                ->subject('Nueva Cita Reservada')        
                ->markdown('mail.appointment.new', ['appointment' => $this->appointment]);
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
            "message" => "Cita reservada " . $this->appointment->date,
            "link" => $this->callback ? $this->callback : "/agenda",
            "title" => "Cita reservada",
            "body" => "El paciente ". Optional($this->appointment->patient)->fullname." ha reservado una cita para el " . Carbon::parse($this->appointment->start)->format('Y-m-d h:i:s A')
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([

            "data" => [
                'message' => "Cita reservada ". $this->appointment->start,
                "link" => $this->callback ? $this->callback : "/agenda?date=" . $this->appointment->date->ToDateString()
            ]
            
        ]);
    }
}

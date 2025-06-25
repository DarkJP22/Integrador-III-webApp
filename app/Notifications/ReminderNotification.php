<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use App\Reminder;
use Illuminate\Support\Carbon;

class ReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $reminder;
    public $callback;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Reminder $reminder, $callback = null)
    {
        $this->reminder = $reminder;
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
        return ['database'];
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
                ->subject('Recordatorio de Cita')        
                ->markdown('mail.appointment.reminder', ['appointment' => $this->reminder->resource]);
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
            "message" => "Recordatorio Cita " . Carbon::parse($this->reminder->resource->start)->format('Y-m-d h:i:s A'),
            "link" => $this->callback ? $this->callback : "/agenda",
            "title" => "Recordatorio Cita",
            "body" => "Esto es un recordatorio que tienes un cita a nombre de {$this->reminder->resource->patient?->first_name} el " . Carbon::parse($this->reminder->resource->start)->format('Y-m-d h:i:s A')
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([]);
    }
}

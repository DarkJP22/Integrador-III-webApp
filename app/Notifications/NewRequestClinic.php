<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use App\RequestOffice;

class NewRequestClinic extends Notification
{
    use Queueable;

    public $requestOffice;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(RequestOffice $requestOffice)
    {
        $this->requestOffice = $requestOffice;
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
            ->subject('Nueva solicitud de integración de clínica')
            ->markdown('mail.clinic.request',[
                'requestOffice' => $this->requestOffice
            ]);
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
            "message" => "Nueva Solicitud de integración de clínica",
            "link" => "/admin/clinics/requests"
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([

            "data" => [
                'message' => "Nueva Solicitud de integración de clínica",
                'link' => "/admin/clinics/requests"
            ]

        ]);
    }
}

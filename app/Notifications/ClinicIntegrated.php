<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use App\RequestOffice;

class ClinicIntegrated extends Notification
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
        return ['mail', 'database', 'broadcast'];
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
            ->subject('Integración de clínica realizada')
            ->markdown('mail.clinic.integrated', ['requestOffice' => $this->requestOffice]);
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
            "message" => "Integración de clínica realizada",
            "link" => "/medic/offices",
            "title" => "Integración de clínica realizada",
            "body" => "Ya puedes agregar la clínica ". $this->requestOffice->name ." a tu perfil para programar y recibir citas"
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([

            "data" => [
                'message' => "Integración de clínica realizada",
                'link' => "/medic/offices"
            ]

        ]);
    }
}

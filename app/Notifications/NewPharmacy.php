<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Pharmacy;
use App\User;

class NewPharmacy extends Notification
{
    use Queueable;

    public $user;
    public $pharmacy;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user, Pharmacy $pharmacy)
    {
        $this->user = $user;
        $this->pharmacy = $pharmacy;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
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
                ->subject('Nuevo administrador de Farmacia Registrado')        
                ->markdown('mail.pharmacy.new', [
                    'user' => $this->user,
                    'pharmacy' => $this->pharmacy
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
            //
        ];
    }
}

<?php

namespace App\Notifications;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewSubscriptionPharmacyFacturacion extends Notification
{
    use Queueable;

    public $user;
    public $purchaseOperationNumber;
    
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user, $purchaseOperationNumber)
    {
        $this->user = $user;
        $this->purchaseOperationNumber = $purchaseOperationNumber;
       
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
            ->subject('Nueva Subscripcion farmacia + facturación')      
            ->markdown('mail.support.newSubscriptionPharmacyFacturacion',[
                'user' => $this->user,
                'purchaseOperationNumber' => $this->purchaseOperationNumber,
                
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

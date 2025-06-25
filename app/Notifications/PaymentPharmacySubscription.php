<?php

namespace App\Notifications;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class PaymentPharmacySubscription extends Notification
{
    use Queueable;

    public $plan;
    public $purchaseOperationNumber;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($plan, $purchaseOperationNumber)
    {
        $this->plan = $plan;
        $this->purchaseOperationNumber = $purchaseOperationNumber;

        $this->administrators = User::whereHas('roles', function ($query) {
            $query->where('name', 'administrador');
        })->get();
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
            ->subject('Confirmación de Subscripcion farmacia + facturación')      
            ->markdown('mail.payment.subscriptionPharmacyConfirmation',[
                'purchaseOperationNumber' => $this->purchaseOperationNumber,
                'plan' => $this->plan,
                'admins' => $this->administrators
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

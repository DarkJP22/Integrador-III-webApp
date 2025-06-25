<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class PaymentSubscriptionConfirmation extends Notification implements ShouldQueue
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
            ->subject('Confirmación de pago')      
            ->markdown('mail.payment.subscriptionConfirmation',[
                'purchaseOperationNumber' => $this->purchaseOperationNumber,
                'plan' => $this->plan
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

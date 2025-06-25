<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class PaymentExpedientCodeConfirmation extends Notification
{
    use Queueable;

    public $code;
    public $description;
    public $amount;
    public $purchaseOperationNumber;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($code, $description, $amount, $purchaseOperationNumber)
    {
        $this->code = $code;
        $this->description = $description;
        $this->amount = $amount;
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
            ->markdown('mail.payment.expedientCodeConfirmation',[
                'purchaseOperationNumber' => $this->purchaseOperationNumber,
                'code' => $this->code,
                'description' => $this->description,
                'amount' =>$this->amount
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

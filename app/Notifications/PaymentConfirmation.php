<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class PaymentConfirmation extends Notification implements ShouldQueue
{
    use Queueable;

    public $incomes;
    public $description;
    public $purchaseOperationNumber;
    public $total;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($incomes, $description, $purchaseOperationNumber, $total)
    {
        $this->incomes = $incomes;
        $this->description = $description;
        $this->purchaseOperationNumber = $purchaseOperationNumber;
        $this->total = $total;
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
            ->markdown('mail.payment.confirmation',[
                'incomes' => $this->incomes,
                'description' => $this->description,
                'purchaseOperationNumber' => $this->purchaseOperationNumber,
                'total' => $this->total
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

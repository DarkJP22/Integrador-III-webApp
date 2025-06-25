<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use App\Invoice;

class NewInvoice extends Notification
{
    use Queueable;

    public $invoice;
    public $callback;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Invoice $invoice, $callback = null)
    {
        $this->invoice = $invoice;
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
        return ['database', 'broadcast'];
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
            "message" => "Nueva Factura ingresada " . $this->invoice->created_at,
            "link" => $this->callback ? $this->callback : "/invoices"
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([

            "data" => [
                'message' => "Nueva Factura ingresada ". $this->invoice->start,
                "link" => $this->callback ? $this->callback : "/invoices"
            ]
            
        ]);
    }
}

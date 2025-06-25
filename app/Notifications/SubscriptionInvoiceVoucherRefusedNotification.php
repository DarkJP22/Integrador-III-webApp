<?php

namespace App\Notifications;

use App\SubscriptionInvoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionInvoiceVoucherRefusedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public SubscriptionInvoice $invoice)
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Comprobante de factura rechazado')
            ->markdown('mail.subscriptionInvoices.invoiceRefused', ['invoice'=> $this->invoice]);
    }

    public function toArray($notifiable): array
    {
        return [];
    }
}

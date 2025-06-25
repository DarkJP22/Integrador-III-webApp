<?php

namespace App\Notifications;

use App\SubscriptionInvoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionInvoiceGenerated extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public SubscriptionInvoice $subscriptionInvoice)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Factura de cobro de subscripción')
                    ->attachData($this->getPDF(), 'invoice.pdf', [
                        'mime' => 'application/pdf',
                    ])
                    ->greeting('Hola')
                    ->line('Se ha generado una factura de cobro por tu subscripción.')
                    ->line('¡Gracias por usar nuestra plataforma!');
    }

    protected function getPDF(): string
    {
        $pdf = Pdf::loadView('subscriptionInvoices.pdf', [
            'invoice' => $this->subscriptionInvoice->load('items','customer', 'currency'),
        ]);

        return $pdf->output();
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            "message" => "Factura de cobro de subscripción generada " . $this->subscriptionInvoice->invoice_date,
            "link" => "/medic/profiles?tab=payments",
            "title" => "Factura de cobro de subscripción",
            "body" => "Se genero la factura de cobro de tu subscripción el " . $this->subscriptionInvoice->invoice_date,
        ];
    }
}

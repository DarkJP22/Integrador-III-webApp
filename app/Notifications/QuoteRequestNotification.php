<?php

namespace App\Notifications;

use App\QuoteOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class QuoteRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public QuoteOrder $quoteOrder, public ?string $callback = null)
    {
    }

    public function via($notifiable): array
    {
        return ['broadcast', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Nueva cotización de boleta')
            ->markdown('mail.quoteOrder.new', ['quoteOrder' => $this->quoteOrder]);
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage([

            "data" => [
                'message' => "El paciente ". Optional($this->quoteOrder->patient)->fullname." ha solicitado una cotización de boleta. Revisala en la pestaña de Cotizaciones de boletas",
                "link" => $this->callback ?? "/lab/quotes?q=" . $this->quoteOrder->patient?->ide
            ]

        ]);
    }
}

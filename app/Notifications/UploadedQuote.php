<?php

namespace App\Notifications;

use App\QuoteOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UploadedQuote extends Notification implements ShouldQueue
{
    use Queueable;

    public $quote;
    public $callback;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(QuoteOrder $quote, $callback = null)
    {
        $this->quote = $quote;
        $this->callback = $callback;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            "message" => "Se ha subido la cotización de la boleta solicitada el  " . $this->quote->created_at,
            "link" => $this->callback ? $this->callback : "/notifications",
            "title" => "Cotización Agregada",
            "body" => "Se ha subido la cotización de la boleta solicitada. Puedes descargarla del siguiente enlace: <a href='". $this->quote->quote_url ."' title='Descargar Cotización' target='_blank'>Descargar Cotización</a>"
        ];
    }
}

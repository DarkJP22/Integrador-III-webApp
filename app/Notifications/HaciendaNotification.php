<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use App\Invoice;

class HaciendaNotification extends Notification
{
    use Queueable;

    public $invoice;
    public $status;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Invoice $invoice, $status)
    {
        $this->invoice = $invoice;
        $this->status = $status;
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
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        // return (new MailMessage)
        //         ->subject('Nueva Cita Reservada')        
        //         ->markdown('mail.appointment.new', ['appointment' => $this->appointment]);
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
            
            "message" => "La Factura " . $this->invoice->NumeroConsecutivo . " tiene estado de " . $this->status . " por parte de hacienda. Verfica por que situación ocurrio entrando en facturacion y verficando el estado",
            "link" => config('app.url') . '/invoices'
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([

            "data" => [
                
                "message" => "La Factura " . $this->invoice->NumeroConsecutivo . " tiene estado de " . $this->status . " por parte de hacienda. Verfica por que situación ocurrio entrando en facturacion y verficando el estado",
                "link" => config('app.url') . '/invoices'
            ]
            
        ]);
    }
}

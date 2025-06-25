<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Invoice;

class SendInvoice extends Mailable
{
    use Queueable, SerializesModels;

    public $pdf;
    protected $xml;
    protected $xmlMensajeHacienda;
    public $invoice;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($pdf, $xml, $xmlMensajeHacienda, Invoice $invoice)
    {
        $this->pdf = $pdf;
        $this->invoice = $invoice;
        $this->xml = $xml;
        $this->xmlMensajeHacienda = $xmlMensajeHacienda;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $name = $this->invoice->NumeroConsecutivo ? $this->invoice->NumeroConsecutivo : $this->invoice->consecutivo;

        if($this->xmlMensajeHacienda){
        
            return $this->subject('Comprobante de factura')->markdown('mail.invoice.comprobante')
                    ->attachData($this->pdf, $name .'.pdf', [
                        'mime' => 'application/pdf',
                    ])
                    ->attachData($this->xml, 'gpsm_'. $this->invoice->clave_fe . '_signed.xml',[
                        'mime' => 'application/xml',
                    ])
                    ->attachData($this->xmlMensajeHacienda, 'gpsm_mensaje_hacienda_' . $this->invoice->clave_fe . '.xml', [
                        'mime' => 'application/xml',
                    ]);
        }else{
            return $this->subject('Comprobante de factura')->markdown('mail.invoice.comprobante')
                ->attachData($this->pdf, $name .'.pdf', [
                    'mime' => 'application/pdf',
                ])
                ->attachData($this->xml, 'gpsm_' . $this->invoice->clave_fe . '_signed.xml', [
                    'mime' => 'application/xml',
                ]);
        }
       
    }
}


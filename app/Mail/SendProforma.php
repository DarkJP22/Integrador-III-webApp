<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Proforma;

class SendProforma extends Mailable
{
    use Queueable, SerializesModels;

    public $pdf;
    public $proforma;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($pdf, Proforma $proforma)
    {
        $this->pdf = $pdf;
        $this->proforma = $proforma;
      

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $name = $this->proforma->NumeroConsecutivo ? $this->proforma->NumeroConsecutivo : $this->proforma->consecutivo;

       
            return $this->subject('Comprobante de proforma')->markdown('mail.proforma.comprobante')
                ->attachData($this->pdf, $name .'.pdf', [
                    'mime' => 'application/pdf',
                ]);
        
       
    }
}


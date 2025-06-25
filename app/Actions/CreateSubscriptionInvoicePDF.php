<?php

namespace App\Actions;

use App\SubscriptionInvoice;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CreateSubscriptionInvoicePDF
{
    public function __invoke(SubscriptionInvoice $subscriptionInvoice): string
    {
        if($subscriptionInvoice->customer_id !== auth()->user()->id){
            abort(403, 'No tienes permiso para descargar este comprobante');
        }

        $data = [
            'invoice' => $subscriptionInvoice->load('items','customer', 'currency'),
        ];

        $pdf = \PDF::loadView('subscriptionInvoices.pdf', $data);//->setPaper('a4', 'landscape');
        $fileName = $subscriptionInvoice->invoice_number.'-'.$subscriptionInvoice->customer_id.'.pdf';

        Storage::disk('s3')->put('/subscription-invoices/pdf/'.$fileName, $pdf->output());

        return Storage::disk('s3')->url('/subscription-invoices/pdf/'.$fileName);
    }
}
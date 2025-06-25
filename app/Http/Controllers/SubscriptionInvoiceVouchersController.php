<?php

namespace App\Http\Controllers;

use App\Actions\UploadVoucherSubscriptionInvoice;
use App\Http\Resources\SubscriptionInvoiceResource;
use App\SubscriptionInvoice;

class SubscriptionInvoiceVouchersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(SubscriptionInvoice $subscriptionInvoice, UploadVoucherSubscriptionInvoice $uploadVoucherSubscriptionInvoice)
    {

        $subscriptionInvoice = $uploadVoucherSubscriptionInvoice($subscriptionInvoice, request()->all());

        flash('Comprobante Subido Correctamente', 'success');

        return SubscriptionInvoiceResource::make($subscriptionInvoice->load('customer', 'items.taxes', 'currency'));
    }
}

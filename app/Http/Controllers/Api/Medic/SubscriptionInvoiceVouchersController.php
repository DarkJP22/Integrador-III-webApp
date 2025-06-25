<?php

namespace App\Http\Controllers\Api\Medic;

use App\Actions\UploadVoucherSubscriptionInvoice;
use App\Http\Controllers\Controller;
use App\Http\Resources\SubscriptionInvoiceResource;
use App\SubscriptionInvoice;


class SubscriptionInvoiceVouchersController extends Controller
{

    public function store(SubscriptionInvoice $subscriptionInvoice, UploadVoucherSubscriptionInvoice $uploadVoucherSubscriptionInvoice)
    {

        $subscriptionInvoice = $uploadVoucherSubscriptionInvoice($subscriptionInvoice, request()->all());


        return SubscriptionInvoiceResource::make($subscriptionInvoice->load('customer', 'items.taxes', 'currency'));
    }
}

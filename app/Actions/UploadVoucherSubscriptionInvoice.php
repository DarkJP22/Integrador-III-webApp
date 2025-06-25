<?php

namespace App\Actions;


use App\Enums\SubscriptionInvoicePaidStatus;
use App\SubscriptionInvoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class UploadVoucherSubscriptionInvoice
{
    public function __invoke(SubscriptionInvoice $subscriptionInvoice, array $data): SubscriptionInvoice
    {
        $data = Validator::validate($data, [
            'voucher' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png']
        ]);

        $subscriptionInvoice = DB::transaction(function () use ($subscriptionInvoice) {
            // save the file in storage
            $path = request()->file('voucher')->store('subscription-invoices', 's3');

            if (!$path) {
                throw new \Exception("The file could not be saved.", 500);
            }

            $subscriptionInvoice->comprobante = $path;
            $subscriptionInvoice->paid_status = SubscriptionInvoicePaidStatus::CHECKING;
            $subscriptionInvoice->paid_at = now();
            $subscriptionInvoice->save();


            return $subscriptionInvoice;
        });


        return $subscriptionInvoice->load('customer', 'items.taxes', 'currency');

    }
}
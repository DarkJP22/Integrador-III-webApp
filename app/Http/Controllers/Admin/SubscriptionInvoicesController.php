<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SubscriptionInvoicePaidStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\SubscriptionInvoiceResource;
use App\Notifications\SubscriptionInvoiceVoucherRefusedNotification;
use App\SubscriptionInvoice;
use Illuminate\Validation\Rules\Enum;

class SubscriptionInvoicesController extends Controller
{
    public function index()
    {

        if(request()->wantsJson()){
            return SubscriptionInvoiceResource::collection(SubscriptionInvoice::with('customer.subscription.plan', 'customer.specialities','items.taxes', 'currency')
                ->search(request(['q']))
                ->when(request('status'), fn($query, $status) => $query->where('paid_status', $status) )
                ->latest()
                ->paginate());
        }

        return view('admin.subscriptionInvoices.index',[

        ]);

    }

    public function update(SubscriptionInvoice $subscriptionInvoice)
    {
        $data = request()->validate([
            'paid_status' => ['sometimes', 'required', new Enum(SubscriptionInvoicePaidStatus::class)]
        ]);


        $subscriptionInvoice->update([
            ...$data
        ]);

        $customer = $subscriptionInvoice->customer;
        $pendingSubscriptionInvoices = $customer->subscriptionInvoices()
            ->where('due_date', '<', today())
            ->where(function ($query) {
                $query->where('paid_status', SubscriptionInvoicePaidStatus::REFUSED)
                    ->orWhere('paid_status', SubscriptionInvoicePaidStatus::UNPAID)
                    ->orWhere('paid_status', SubscriptionInvoicePaidStatus::CHECKING);
            })
            ->count();

        if($subscriptionInvoice->paid_status === SubscriptionInvoicePaidStatus::PAID && $pendingSubscriptionInvoices === 0){
            $customer?->update([
                'disabled_by_payment' => false
            ]);
        }
        if($data['paid_status'] === SubscriptionInvoicePaidStatus::REFUSED->value){
            $customer?->notify(new SubscriptionInvoiceVoucherRefusedNotification($subscriptionInvoice));
        }

        return SubscriptionInvoiceResource::make($subscriptionInvoice->load('customer.subscription.plan', 'customer.specialities','items.taxes', 'currency'));
    }
}

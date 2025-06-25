<?php

namespace App\Http\Controllers;

use App\Actions\CreateSubscriptionInvoicePDF;
use App\Http\Resources\SubscriptionInvoiceResource;
use App\SubscriptionInvoice;
use Illuminate\Support\Facades\Storage;


class SubscriptionInvoicesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return SubscriptionInvoiceResource::collection(SubscriptionInvoice::with('customer.subscription.plan', 'customer.specialities', 'items.taxes', 'currency')
            ->search(request(['q']))
            ->when(request('status'), fn($query, $status) => $query->where('paid_status', $status) )
            ->where('customer_id', auth()->user()->id)
            ->latest()
            ->paginate());

    }

    public function pdf(SubscriptionInvoice $subscriptionInvoice, CreateSubscriptionInvoicePDF $createSubscriptionInvoicePDF)
    {
        return redirect($createSubscriptionInvoicePDF($subscriptionInvoice));
    }
}

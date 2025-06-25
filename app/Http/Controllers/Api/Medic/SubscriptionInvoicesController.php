<?php

namespace App\Http\Controllers\Api\Medic;

use App\Actions\CreateSubscriptionInvoicePDF;
use App\Http\Controllers\Controller;
use App\SubscriptionInvoice;
use Illuminate\Support\Facades\Storage;

class SubscriptionInvoicesController extends Controller
{
    public function index()
    {
        return SubscriptionInvoice::with('customer', 'items.taxes', 'currency')
            ->search(request(['q']))
            ->when(request('status'), fn($query, $status) => count(array_filter($status)) ? $query->whereIn('paid_status', $status) : $query)
            ->where('customer_id', auth()->user()->id)
            ->latest()
            ->limit(10)
            ->get();

    }

    public function pdf(SubscriptionInvoice $subscriptionInvoice, CreateSubscriptionInvoicePDF $createSubscriptionInvoicePDF)
    {
        return $createSubscriptionInvoicePDF($subscriptionInvoice);
    }

}

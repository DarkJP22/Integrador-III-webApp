<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionInvoiceItemTax extends Model
{
    protected $guarded = [];

    public function subscriptionInvoice(): BelongsTo
    {
        return $this->belongsTo(SubscriptionInvoice::class, 'subscription_invoice_id');
    }

    public function subscriptionInvoiceItem(): BelongsTo
    {
        return $this->belongsTo(SubscriptionInvoiceItem::class, 'subscription_invoice_item_id');
    }
}

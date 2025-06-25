<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionInvoiceItem extends Model
{
    protected $guarded = [];

    protected $casts = [
        'quantity' => 'float',
    ];
    public function subscriptionInvoice(): BelongsTo
    {
        return $this->belongsTo(SubscriptionInvoice::class);
    }

    public function taxes(): HasMany
    {
        return $this->hasMany(SubscriptionInvoiceItemTax::class, 'subscription_invoice_item_id');
    }
}

<?php

namespace App;

use App\Enums\SubscriptionInvoicePaidStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class SubscriptionInvoice extends Model
{
    protected $guarded = [];
    protected $appends = ['paid_status_label', 'comprobante_url'];
    protected $casts = [
        'paid_status' => SubscriptionInvoicePaidStatus::class,
    ];

    public static function createItems($invoice, $invoiceItems): void
    {


        foreach ($invoiceItems as $invoiceItem) {


            $item = $invoice->items()->create($invoiceItem);

            if (array_key_exists('taxes', $invoiceItem) && $invoiceItem['taxes']) {
                foreach ($invoiceItem['taxes'] as $tax) {

                    if (gettype($tax['amount']) !== "NULL") {
                        $item->taxes()->create($tax);
                    }
                }
            }

        }
    }

    public function items(): HasMany
    {
        return $this->hasMany(SubscriptionInvoiceItem::class, 'subscription_invoice_id');
    }

    public static function createTaxes($invoice, $taxes): void
    {

        foreach ($taxes as $tax) {

            if (gettype($tax['amount']) !== "NULL") {

                $invoice->taxes()->create($tax);
            }
        }
    }

    public function getPaidStatusLabelAttribute()
    {
        return $this->paid_status->label();
    }

    public function getComprobanteUrlAttribute()
    {
        return $this->comprobante
            ? Storage::disk('s3')->url($this->comprobante)
            : null;
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function scopeSearch($query, $search)
    {
        if (isset($search['q']) && $search['q']) {

            $query = $query->where(function ($query) use ($search) {
                $query->where('invoice_number', 'like', '%'.$search['q'].'%');
                $query->orWhereHas('customer', function ($query) use ($search) {
                    $query->where('ide', 'like', '%'.$search['q'].'%')
                        ->orWhere('name', 'like', '%'.$search['q'].'%');
                });
            });

        }

        return $query;
    }
}

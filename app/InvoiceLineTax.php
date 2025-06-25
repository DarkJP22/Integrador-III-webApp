<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InvoiceLineTax extends Model
{
    protected $guarded = [];
    protected $table = 'invoice_line_taxes';
    public $timestamps = false;

    public function invoiceLine()
    {
        return $this->belongsTo(InvoiceLine::class);
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceLineDiscount extends Model
{
    use HasFactory;

    protected $guarded = ['NoEditable'];
    protected $table = 'invoice_line_discounts';
    public $timestamps = false;

    public function invoiceLine()
    {
        return $this->belongsTo(InvoiceLine::class);
    }
}

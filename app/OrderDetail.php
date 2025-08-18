<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $fillable = [
        'order_id',
        'drug_id',
        'requested_amount',
        'quantity_available',
        'unit_price',
        'iva_percentage',
        'description',
        'products_total'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'iva_percentage' => 'decimal:2',
        'products_total' => 'decimal:2',
        'requested_amount' => 'integer',
        'quantity_available' => 'integer',
    ];

    // Relaciones
    public function order()
    {
        return $this->belongsTo(Orders::class, 'order_id');
    }

    public function drug()
    {
        return $this->belongsTo(Drug::class);
    }

    // Accessors
    public function getCalculatedTotalAttribute()
    {
        $cantidad = $this->requested_amount > $this->quantity_available 
            ? $this->quantity_available 
            : $this->requested_amount;
        
        return $cantidad * $this->unit_price;
    }

    // Mutators
    public function setUnitPriceAttribute($value)
    {
        $this->attributes['unit_price'] = max(0, floatval($value));
    }

    public function setQuantityAvailableAttribute($value)
    {
        $this->attributes['quantity_available'] = max(0, intval($value));
    }
}

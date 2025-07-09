<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    use HasFactory;

    protected $fillable = [
        'consecutive',
        'pharmacy_id',
        'user_id',
        'date',
        'status',
        'payment_method',
        'requires_shipping',
        'address',
        'lat',
        'lot',
        'order_total',
        'shipping_total',
        'voucher'
    ];

    protected $casts = [
        'date' => 'datetime',
        'payment_method' => 'boolean',
        'requires_shipping' => 'boolean',
        'order_total' => 'decimal:2',
        'shipping_total' => 'decimal:2',
        'lat' => 'decimal:8',
        'lot' => 'decimal:8',
    ];

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class);
    }

    public function details()
    {
        return $this->hasMany(OrderDetail::class, 'order_id');
    }

    // Accessors
    public function getProductsTotalAttribute()
    {
        return $this->details->sum('products_total');
    }

    public function getPaymentMethodTextAttribute()
    {
        return $this->payment_method ? 'SINPE' : 'Efectivo';
    }

    public function getStatusTextAttribute()
    {
        $statuses = [
            'cotizacion' => 'Cotización',
            'esperando_confirmacion' => 'Esperando Confirmación',
            'confirmado' => 'Confirmado',
            'preparando' => 'Preparando',
            'cancelado' => 'Cancelado',
            'despachado' => 'Despachado',
        ];

        return $statuses[$this->status] ?? ucfirst($this->status);
    }

    // Scopes
    public function scopeByPharmacy($query, $pharmacyId)
    {
        return $query->where('pharmacy_id', $pharmacyId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}

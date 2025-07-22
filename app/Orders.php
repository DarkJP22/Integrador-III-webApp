<?php

namespace App;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\ShippingRequired;
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
        'status' => OrderStatus::class,
        'payment_method' => PaymentMethod::class,
        'requires_shipping' => ShippingRequired::class,
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
        return $this->payment_method->label();
    }

    public function getStatusTextAttribute()
    {
        return $this->status->label();
    }

    // Scopes
    public function scopeByPharmacy($query, $pharmacyId)
    {
        return $query->where('pharmacy_id', $pharmacyId);
    }

    public function scopeByStatus($query, $status)
    {
        if ($status instanceof OrderStatus) {
            return $query->where('status', $status->value);
        }
        
        return $query->where('status', $status);
    }

    // Métodos de conveniencia para trabajar con Enums

    // Método para verificar si el método de pago es electrónico
    public function isElectronicPayment(): bool
    {
        return $this->payment_method->isElectronic();
    }
    // Método para verificar si el pedido requiere envío
    public function requiresShipping(): bool
    {
        return $this->requires_shipping->requiresShipping();
    }
}

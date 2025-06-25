<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Commission extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    protected $appends = [
        'comprobante_url',
    ];

    public function getComprobanteUrlAttribute()
    {
        return $this->comprobante
                    ? Storage::disk('s3')->url($this->comprobante)
                    : '';
    }

    public function scopePending($query)
    {
        $query->whereNull('paid_at');
    }

    public function scopePaid($query)
    {
        $query->whereNotNull('paid_at');
    }

    public function transactions()
    {
        return $this->hasMany(CommissionTransaction::class, 'commission_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

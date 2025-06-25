<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommissionTransaction extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function scopePending($query)
    {
        $query->whereNull('paid_at');
    }

    public function scopePaid($query)
    {
        $query->whereNotNull('paid_at');
    }

    public function resource()
    {
        return $this->morphTo();
    }

    public function commission()
    {
        return $this->belongsTo(Commission::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
}

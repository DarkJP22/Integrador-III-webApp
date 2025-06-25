<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable  = ['plan_id','cost','quantity','ends_at', 'purchase_operation_number', 'previous_billing_date'];
    protected $appends = ['costName'];
    protected $casts = [
        'ends_at' => 'datetime',
        'previous_billing_date' => 'date',
    ];

    public function getCostNameAttribute()
    {
        return $this->cost > 0 ? (money($this->cost,'$', 0) .' / '. $this->quantity. ' Mes(es)') : 'Gratis';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

}

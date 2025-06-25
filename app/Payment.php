<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Payment extends Model
{
    protected $guarded = [];
    protected $casts = [
        'amount' => 'float',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($payment) {
           
            $payment->invoice->calculatePendingAmount();
            
          
        });
    }

    public function scopeSearch($query, $search)
    {
        if (isset($search['q']) && $search['q']) {

            $query = $query->where(function ($query) use ($search) {
                $query->whereHas('invoice', function ($query) use ($search) {
                    $query->where('cliente', 'like', '%' . $search['q'] . '%')
                        ->orWhere('identificacion_cliente', 'like', '%' . $search['q'] . '%');

                });
            });
        }

        if (isset($search['start']) && $search['start']) {
            $start = new Carbon($search['start']);
            $end = (isset($search['end']) && $search['end'] != "") ? $search['end'] : $search['start'];
            $end = new Carbon($end);

            $query = $query->where([
                ['date', '>=', $start],
                ['date', '<=', $end->endOfDay()]
            ]);
        }

        return $query;
    }


    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}

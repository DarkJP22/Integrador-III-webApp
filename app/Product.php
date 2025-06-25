<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $guarded = ['photo', 'taxes'];

    /**
     * Boot the product instance.
     */
    protected static function boot()
    {
        parent::boot();
        static::created(function ($product) {
            if (request('taxes')) {
                $product->taxes()->sync(request('taxes'));

            }
            $product->calculatePriceWithTaxes();
        });



    }
    // protected $appends = ['PriceWithTaxes'];

    public function taxes()
    {
        return $this->belongsToMany(Tax::class);
    }

    public function calculatePriceWithTaxes()
    {
        $montoTarifaTotal = 0;


        foreach ($this->taxes as $tax) {


            $montoTarifa = 0;

            $tarifa = $tax->tarifa / 100;
            $montoTarifa = ($this->price * $tarifa);


            $montoTarifaTotal += $montoTarifa;

        }

        $this->update([
            'priceWithTaxes' => $montoTarifaTotal ? ($this->price + $montoTarifaTotal) : $this->price,
            'taxesAmount' => $montoTarifaTotal,
        ]);

    }

    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            });
        }

        return $query;
    }

    /**
     * Determine if the user has the given role.
     *
     * @param  mixed $role
     * @return boolean
     */
    public function hasTax($tax)
    {
        if($tax instanceof Tax){
            $tax = $tax->code;
        }
       
        return $this->taxes->contains('code', $tax);
       
    }
}


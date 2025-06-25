<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProformaLine extends Model
{
    protected $guarded = ['taxes','PrecioOriginalTemp', 'product', 'overrideImp', 'discounts'];
    protected $table = 'proforma_lines';
    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($line) {
            $line->taxes->each->delete();
            $line->discounts->each->delete();
        });

    }

    public function proforma()
    {
        return $this->belongsTo(Proforma::class);
    }

    public function taxes()
    {
        return $this->hasMany(ProformaLineTax::class);
    }

    public function saveTaxes($items)
    {

        return $this->taxes()->createMany($items);
    }

    public function discounts()
    {
        return $this->hasMany(ProformaLineDiscount::class);
    }

    public function saveDiscounts($items)
    {
        $validDiscounts = collect($items)->filter(function ($item, $key) {
            return $item["PorcentajeDescuento"] > 0;
        });

        return $this->discounts()->createMany($validDiscounts->all());
    }
}

<?php

namespace App;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class Proforma extends Model
{
    protected $guarded = ['lines', 'TipoDocumentoName', 'CondicionVentaName', 'MedioPagoName', 'initialPayment', 'user'];


    protected $appends = ['TipoDocumentoName', 'CondicionVentaName', 'MedioPagoName'];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function getTipoDocumentoNameAttribute()
    {
        return trans('utils.tipo_documento.' . $this->TipoDocumento);
    }
    public function getCondicionVentaNameAttribute()
    {
        return trans('utils.condicion_venta.' . $this->CondicionVenta);
    }
    public function getMedioPagoNameAttribute()
    {
        return trans('utils.medio_pago.' . $this->MedioPago);
    }

    public function scopeSearch($query, $search)
    {
        if (isset($search['q']) && $search['q']) {

            $query = $query->where(function ($query) use ($search) {
                $query->where('cliente', 'like', '%' . $search['q'] . '%')
                    ->orWhere('identificacion_cliente', 'like', '%' . $search['q'] . '%')
                    ->orWhere('consecutivo', 'like', '%' . $search['q'] . '%');
            });
        }

        if (isset($search['start']) && $search['start']) {
            $start = new Carbon($search['start']);
            $end = (isset($search['end']) && $search['end'] != "") ? $search['end'] : $search['start'];
            $end = new Carbon($end);

            $query = $query->where([
                ['created_at', '>=', $start],
                ['created_at', '<=', $end->endOfDay()]
            ]);
        }
        if (isset($search['office']) && $search['office']) {

            $query = $query->where('office_id', $search['office']);


        }
        if (isset($search['medic']) && $search['medic']) {

            $query = $query->where('user_id', $search['medic']);


        }

        if (isset($search['type']) && $search['type']) {

            $query = $query->where('TipoDocumento', $search['type']);


        }
        if (isset($search['condicion']) && $search['condicion']) {

            $query = $query->where('CondicionVenta', $search['condicion']);


        }


        return $query;
    }

    public function lines()
    {
        return $this->hasMany(ProformaLine::class);
    }
   

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function customer()
    {
        return $this->belongsTo(Patient::class, 'customer_id');
    }

    public function clinic()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }


    public function saveLines($items)
    {


        foreach ($items as $item) {

            $item = Arr::except($item, array('id', 'proforma_id'));

            $line = $this->lines()->create($item);
            $line->saveTaxes($item['taxes']);
            $line->saveDiscounts($item['discounts']);


        }

        return $this;
    }


}

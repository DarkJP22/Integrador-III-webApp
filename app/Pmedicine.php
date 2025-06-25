<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Pmedicine extends Model
{
    public static $searchable = ['name', 'date_purchase'];
    protected $fillable = [
        'name', 'date_purchase', 'remember', 'remember_days', 'pharmacy_id', 'user_id', 'receta'
    ];
    protected $casts = [
        'date_purchase' => 'datetime',
    ];

    public function scopeSearch($query, $search)
    {
        if (isset($search['q']) && $search['q']) {
            $query->where(function ($query) use ($search) {
                $query->Where('name', 'like', '%'.$search['q'].'%')
                    ->orWhereHas('patient', function ($query) use ($search) {
                        $query->where('first_name', 'like', '%'.$search['q'].'%')
                            ->orWhere('last_name', 'like', '%'.$search['q'].'%');

                    });
            });
        }

        if (isset($search['start']) && $search['start']) {
            $start = new Carbon($search['start']);
            $end = (isset($search['end']) && $search['end'] != "") ? $search['end'] : $search['start'];
            $end = new Carbon($end);

            $query = $query->where([
                ['date_purchase', '>=', $start],
                ['date_purchase', '<=', $end->endOfDay()]
            ]);
        }

        return $query;
    }

    public function getDatePurchaseAttribute()
    {
        return Carbon::parse($this->attributes['date_purchase'])->format('Y-m-d');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class);
    }

    public function dosesreminder()
    {
        return $this->hasOne(Dosereminder::class, 'medicine_id');
    }

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}

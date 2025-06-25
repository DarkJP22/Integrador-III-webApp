<?php

namespace App;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Medicine extends Model
{
    protected $guarded = [];

    protected $casts = [
        'date_purchase' => 'datetime',
        'reminder_start' => 'datetime',
        'reminder_end' => 'datetime',
        'active_remember_for_days' => 'integer',
        'remember' => 'boolean',
        'remember_days' => 'integer',
        'requested_units' => 'integer',
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

    public function dosesreminder()
    {
        return $this->hasOne(Dosereminder::class);
    }

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    protected function name(): Attribute
    {
        return Attribute::make(
        //get: fn ($value) => strtoupper($value),
            set: fn($value) => strtoupper($value),
        );
    }
}

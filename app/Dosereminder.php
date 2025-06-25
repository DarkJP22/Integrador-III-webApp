<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dosereminder extends Model
{
    protected $guarded = [];

    public function setHoursAttribute($value)
    {
        $data = serialize($value);
        
        $this->attributes['hours'] = $data;
    }
    public function getHoursAttribute($value)
    {
        return unserialize($value);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $guarded = ['idRemove','office_info','ini','fin'];

    protected function title(): Attribute
    {
        return Attribute::make(
            //get: fn ($value) => strtoupper($value),
            set: fn ($value) => strtoupper($value),
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
     public function office()
    {
        return $this->belongsTo(Office::class);
    }
    /* public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function hasAppointments()
    {
        
        return Appointment::where('schedule_id', $this->id)->count();
    } */
    
}

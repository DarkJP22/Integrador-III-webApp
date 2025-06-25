<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VitalSign extends Model
{
    protected $fillable = [
        'appointment_id','patient_id','height','weight','mass','temp','respiratory_rate','blood_ps','blood_pd','heart_rate','oxygen','glicemia'
    ];
    protected $table = 'vital_signs';

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}

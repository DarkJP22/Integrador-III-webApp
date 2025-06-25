<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Archivo extends Model
{
    protected $fillable = [
        'name','patient_id','appointment_id', 'description'
    ];

    public function getNameAttribute($file)
    {
        return "/storage/patients/". $this->patient_id ."/files/". $file->name;
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
       
    }
   
}

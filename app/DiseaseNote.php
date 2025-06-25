<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DiseaseNote extends Model
{
    
    protected $fillable = [
        'reason','symptoms','phisical_review','revalorizacion'
    ];
    protected $table = 'disease_notes';

     public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}

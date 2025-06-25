<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmergencyContact extends Model
{
    protected $guarded = [];
    protected $table = 'emergency_contacts';
    
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
    
}

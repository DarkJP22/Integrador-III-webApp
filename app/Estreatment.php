<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estreatment extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'facial' => 'array',
        'corporal' => 'array',
        'depilacion' => 'array'
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function optreatment()
    {
        return $this->belongsTo(Optreatment::class);
    }
}

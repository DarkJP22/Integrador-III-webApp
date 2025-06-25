<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anthropometry extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'items' => 'array'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accumulated extends Model
{
    use HasFactory;

    protected $guarded = ['patients'];

    public function patients()
    {
        return $this->belongsToMany(Patient::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function holder()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function transactions()
    {
        return $this->hasMany(AccumulatedTransaction::class);
    }

}

<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Speciality extends Model
{
    use HasFactory;

    protected $guarded = [];

    
	public function users()
    {
        return $this->belongsToMany(User::class);
    }
    
}

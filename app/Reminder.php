<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
     
     protected $guarded  = [];



     public function resource()
     {
         return $this->morphTo();
     }

}

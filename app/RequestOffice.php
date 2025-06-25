<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RequestOffice extends Model
{
    protected $fillable = [
        'name','phone','address'
    ];
    protected $table = 'request_offices';

     public function user()
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Allergy extends Model
{
    protected $guarded = [];

    public function history()
    {
        return $this->belongsTo(History::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

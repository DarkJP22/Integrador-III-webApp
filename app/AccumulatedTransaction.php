<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccumulatedTransaction extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function resource()
    {
        return $this->morphTo();
    }

    public function accumulated()
    {
        return $this->belongsTo(Accumulated::class);
    }
}

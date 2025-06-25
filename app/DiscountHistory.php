<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscountHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by', 'CodigoMoneda','amount','discount','total_discount', 'total', 'description'
    ];
    protected $table = 'discount_histories';

    public function user()
    {
        return $this->belongsTo(user::class);
    }

    public function creator()
    {
        return $this->belongsTo(user::class, 'created_by');
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Oprecomendation extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function scopeSearch($query, $search)
    {
        if (isset($search['q']) && $search['q']) {

            $query = $query->where(function ($query) use ($search) {
              
                $query->where('name', 'like', '%' . $search['q'] . '%')
                     ->orWhere('category', 'like', '%' . $search['q'] . '%');
            });
        }


        return $query;
    }
}

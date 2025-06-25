<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MediaTag extends Model
{
    protected $guarded = [];
    protected $table = 'media_tags';
    public function scopeSearch($query, $search)
    {
        if (isset($search['q']) && $search['q']) {

            $query = $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search['q'] . '%');
            });
        }


        return $query;
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Cierre extends Model
{
    protected $guarded = [];
    protected $casts = ['from' => 'datetime', 'to' => 'datetime'];

    public function scopeSearch($query, $search)
    {

        if (isset($search['start']) && $search['start']) {
            $start = new Carbon($search['start']);
            $end = (isset($search['end']) && $search['end'] != "") ? $search['end'] : $search['start'];
            $end = new Carbon($end);

            $query = $query->where([
                ['to', '>=', $start],
                ['to', '<=', $end->endOfDay()]
            ]);
        }

        if (isset($search['office']) && $search['office']) {

            $query = $query->where('office_id', $search['office']);
        }

        if (isset($search['user']) && $search['user']) {

            $query = $query->where('user_id', $search['user']);
        }

        if (isset($search['archived']) && $search['archived']) {

            $query = $query->whereNotNull('archived_at');
        } else {

            $query = $query->whereNull('archived_at');
        }

        $query = $query->where('CodigoActividad', $search['CodigoActividad']);





        return $query;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function office()
    {
        return $this->belongsTo(Office::class);
    }
}

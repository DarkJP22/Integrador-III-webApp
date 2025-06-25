<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AffiliationPlan extends Model
{
    protected $guarded = [];
    protected $table = 'affiliation_plans';
    public function scopeSearch($query, $search)
    {
        if (isset($search['q']) && $search['q']) {

            $query = $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search['q'] . '%')
                    ->orWhere('cuota', 'like', '%' . $search['q'] . '%');
            });
        }
        if (isset($search['office_id']) && $search['office_id']) {

            $query = $query->where('office_id', $search['office_id']);


        }

        return $query;
    }


    public function affiliations()
    {
        return $this->hasMany(Affiliation::class);
    }
}

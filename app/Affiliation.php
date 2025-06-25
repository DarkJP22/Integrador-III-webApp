<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Affiliation extends Model
{
    protected $guarded = ['patients'];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($affiliation) {
            \DB::table('affiliation_patient')->where('affiliation_id', $affiliation->id)->delete();
            $affiliation->payments->each->delete();
        });

    }

    public function scopeSearch($query, $search)
    {
        if (isset($search['q']) && $search['q']) {

            $query = $query->where(function ($query) use ($search) {
                $query->whereHas('patients', function ($query) use($search) {
                        $query->where('first_name', 'like', '%' . $search['q'] . '%')
                              ->orWhere('ide', $search['q'] );
                });
            });
        }

        if (isset($search['office_id']) && $search['office_id']) {

            $query = $query->where('office_id', $search['office_id']);


        }

        return $query;
    }

    public function patients()
    {
        return $this->belongsToMany(Patient::class);
    }

    public function holder()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }
    public function plan()
    {
        return $this->belongsTo(AffiliationPlan::class, 'affiliation_plan_id');
    }

    public function payments()
    {
        return $this->hasMany(AffiliationPayment::class);
    }
    public function transactions()
    {
        return $this->hasMany(AffiliationTransaction::class);
    }
}

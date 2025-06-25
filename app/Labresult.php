<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Labresult extends Model
{
    protected $fillable = [
        'date','name','patient_id','url', 'description', 'medic_id', 'user_id'
    ];
    protected $appends = ['file_path'];

    public function getFilePathAttribute()
    {

        return $this->name ? \Storage::disk('s3')->url('patients/' . $this->patient_id . '/labresults/' . $this->id.'/'.$this->name) : false;
    }

    public function scopeFilter($query, array $filters)
    {
        collect(str_getcsv($filters['q'] ?? false, ' ', '"'))->filter()->each(function ($term) use ($query) {
            $term = '%' . $term . '%';

            $query->where(function ($query) use ($term) {

                $query->where('name', 'like', $term)
                    ->orWhere('description', 'like', $term);
            });
        });

    
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
       
    }

    public function medic()
    {
        return $this->belongsTo(User::class, 'medic_id');
       
    }


}

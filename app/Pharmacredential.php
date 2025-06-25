<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pharmacredential extends Model
{
    protected $guarded = ['pharmacy'];
   
    protected static function boot()
    {
        parent::boot();

        static::updated(function ($pharmacredential) {
           
          

            \DB::table('apipharmacredentials')->where('pharmacy_id', $pharmacredential->pharmacy_id)
            ->update([
                "name" => $pharmacredential->name,
                "api_url" => $pharmacredential->api_url,
                "access_token" => $pharmacredential->access_token,
            ]);
          
        });

        static::created(function ($pharmacredential) {
     
            if(\DB::table('apipharmacredentials')->where('pharmacy_id', $pharmacredential->pharmacy_id)->count()){

                \DB::table('apipharmacredentials')->where('pharmacy_id', $pharmacredential->pharmacy_id)
                ->update([
                    "name" => $pharmacredential->name,
                    "api_url" => $pharmacredential->api_url,
                    "access_token" => $pharmacredential->access_token,
                ]);

            }else{

                $pharmacy = $pharmacredential->pharmacy;

                foreach ($pharmacy->patients as $patient) {

                    $patient->apipharmacredentials()->create([
                        "name" => $pharmacredential->name,
                        "api_url" => $pharmacredential->api_url,
                        "access_token" => $pharmacredential->access_token,
                        "pharmacy_id" => $pharmacredential->pharmacy_id
                    ]);
                }

            }
            
          
        });

        static::deleting(function ($pharmacredential) {
            \DB::table('apipharmacredentials')->where('pharmacy_id', $pharmacredential->pharmacy_id)
            ->delete();
        });
    }

    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class);
    }
}

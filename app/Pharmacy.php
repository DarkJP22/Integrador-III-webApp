<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pharmacy extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'province',
        'canton',
        'district',
        'city',
        'phone',
        'ide',
        'ide_name',
        'lat',
        'lon',
        'address_map',
        'notification',
        'notification_date',
        'active',
        'logo_path',
        'user_id'
    ];

    protected $appends = ['notification_datetime', 'notification_hour', 'name_address', 'provinceName'];

    /**
     * Get the path to the user's avatar.
     *
     * @param  string $avatar
     * @return string
     */
    public function getLogoPathAttribute($logo)
    {
        if (!$logo) return asset('img/logo.png');


        return asset('storage/' . $logo);
    }
    public function getProvinceNameAttribute()
    {
        return trans('utils.provincias.' . $this->province);
    }

    public function getNotificationDatetimeAttribute()
    {
        return $this->notification_date ? Carbon::parse($this->notification_date)->format('Y-m-d') : '';
    }
    public function getNotificationHourAttribute()
    {
        return $this->notification_date ? Carbon::parse($this->notification_date)->toTimeString() : '';
    }
    public function getNameAddressAttribute()
    {
        return $this->name . ' - ' . $this->province . ', ' . $this->canton;
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
    public function assistants()
    {
        return $this->users()->whereHas('roles', function ($query) {
            $query->where('name',  'asistente');
        })->where('active', 1)->get();
    }

    public function medicineRemiders()
    {
        return $this->hasMany(MedicineReminder::class);
    }

    public function patients()
    {
        return $this->belongsToMany(Patient::class);
    }

    public function pharmacredential()
    {
        return $this->hasOne(Pharmacredential::class);
    }

    public function orders()
    {
        return $this->hasMany(Orders::class);
    }
}

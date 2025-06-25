<?php

namespace App;

use App\Enums\OfficeType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Mockery\Exception;

class Office extends Model
{
    use HasFactory;

    const DISTANCE_UNIT_KILOMETERS = 111.045;
    const DISTANCE_UNIT_MILES = 69.0;

    protected $fillable = [
        'type', 'name', 'address', 'province', 'canton', 'district', 'city', 'phone', 'ide', 'ide_name', 'bill_to', 'lat', 'lon', 'address_map', 'notification',
        'notification_date', 'active', 'fe', 'logo_path', 'created_by', 'original_type', 'whatsapp_number', 'utiliza_agenda_gps', 'settings'
    ];
    protected $casts = [
        'utiliza_agenda_gps' => 'boolean',
        'type' => OfficeType::class,
        'settings' => AsCollection::class,
    ];

    protected $appends = ['notification_datetime', 'notification_hour', 'name_address', 'type_name', 'provinceName', 'fullPhone', 'fullWhatsappPhone'];

    public function getFullPhoneAttribute()
    {
        return '+506'.$this->phone;
    }

    public function getFullWhatsAppPhoneAttribute()
    {
        return '+506'.$this->whatsapp_number;
    }

    /**
     * Get the path to the user's avatar.
     *
     * @param  string  $avatar
     * @return string
     */
    public function getLogoPathAttribute($logo)
    {
        if (!$logo) {
            return asset('img/logo.png');
        }


        return \Storage::disk('s3')->url($logo);
    }

    public function getProvinceNameAttribute()
    {
        return trans('utils.provincias.'.$this->province);
    }

    public function getTypeNameAttribute()
    {
        return trans('utils.office_type.'.$this->type->value);
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
        return $this->name.' - '.$this->provinceName.', '.$this->canton;
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($query) use ($search) {
            $query->where('name', 'like', '%'.$search.'%');
        });
    }

    public function scopeActive($query, $search)
    {

        return $query->where(function ($query) use ($search) {
            $query->where('active', $search);
        });
    }

    public function scopeWithAndWhereHas($query, $relation, $constraint)
    {
        return $query->whereHas($relation, $constraint)
            ->with([$relation => $constraint]);
    }

    /**
     * @param $query
     * @param $lat
     * @param $lng
     * @param $radius numeric
     * @param $units string|['K', 'M']
     */
    public function scopeNearLatLng($query, $lat, $lng, $radius = 10, $units = 'K')
    {

        $query->selectRaw('offices.*, ( 6371 * acos( cos( radians( ? ) ) * cos( radians( lat ) ) * cos( radians( lon ) - radians( ? ) ) + sin( radians( ? ) ) * sin( radians( lat ) ) ) ) AS distance',
            [$lat, $lng, $lat])
            ->having('distance', '<', $radius)
            ->orderBy('distance');

        // SELECT id, ( 3959 * acos( cos( radians(37) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians(-122) ) + sin( radians(37) ) * sin( radians( lat ) ) ) ) AS distance FROM markers HAVING distance < 25 ORDER BY distance LIMIT 0 , 20;

        // $query->select("*"
        //         ,\DB::raw("6371 * acos(cos(radians(" . $lat . "))
        //         * cos(radians(offices.lat))
        //         * cos(radians(offices.lon) - radians(" . $lng . "))
        //         + sin(radians(" .$lat. "))
        //         * sin(radians(offices.lat))) AS distance"));


        // $distanceUnit = $this->distanceUnit($units);

        // if (!(is_numeric($lat) && $lat >= -90 && $lat <= 90)) {
        //     throw new Exception("Latitude must be between -90 and 90 degrees.");
        // }

        // if (!(is_numeric($lng) && $lng >= -180 && $lng <= 180)) {
        //     throw new Exception("Longitude must be between -180 and 180 degrees.");
        // }

        // $haversine = sprintf('*, (%f * DEGREES(ACOS(COS(RADIANS(%f)) * COS(RADIANS(lat)) * COS(RADIANS(%f - lon)) + SIN(RADIANS(%f)) * SIN(RADIANS(lat))))) AS distance',
        //     $distanceUnit,
        //     $lat,
        //     $lng,
        //     $lat
        // );

        // $subselect = clone $query;
        // $subselect
        //     ->selectRaw(\DB::raw($haversine));

        // // Optimize the query, see details here:
        // // http://www.plumislandmedia.net/mysql/haversine-mysql-nearest-loc/

        // $latDistance      = $radius / $distanceUnit;
        // $latNorthBoundary = $lat - $latDistance;
        // $latSouthBoundary = $lat + $latDistance;
        // $subselect->whereRaw(sprintf("lat BETWEEN %f AND %f", $latNorthBoundary, $latSouthBoundary));

        // $lngDistance     = $radius / ($distanceUnit * cos(deg2rad($lat)));
        // $lngEastBoundary = $lng - $lngDistance;
        // $lngWestBoundary = $lng + $lngDistance;
        // $subselect->whereRaw(sprintf("lon BETWEEN %f AND %f", $lngEastBoundary, $lngWestBoundary));

        // /*$query
        //     ->from(\DB::raw('(' . $subselect->toSql() . ') as d'))
        //     ->where('distance', '<=', $radius);*/
        // $query->selectRaw(\DB::raw($haversine))
        //       //->whereRaw(sprintf("lat BETWEEN %f AND %f", $latNorthBoundary, $latSouthBoundary))
        //       //->whereRaw(sprintf("lon BETWEEN %f AND %f", $lngEastBoundary, $lngWestBoundary))
        //       ->having('distance', '<=', $radius);

    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Assign the given role to the user.
     *
     * @param  string  $role
     * @return mixed
     */
    public function assignCurrency($currency)
    {
        return $this->currencies()->sync($currency);
    }

    public function currencies()
    {
        return $this->belongsToMany(Currency::class);
    }

    public function activeMedics()
    {
        return $this->belongsToMany(User::class)->whereHas('roles', function ($query) {
            $query->where('name', 'medico')
                ->orWhere('name', 'esteticista');
        });
    }

    public function medics($date1, $date2)
    {

        return $this->users()->with([
            'incomes' => function ($query) use ($date1, $date2) {
                $query->where('type', 'I')
                    ->where([
                        ['incomes.date', '>=', $date1->startOfDay()],
                        ['incomes.date', '<=', $date2->endOfDay()]
                    ]);
            }
        ])->whereHas('roles', function ($query) {
            $query->where('name', 'medico');
        })->where('active', 1)->get();
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function medicsWithInvoices($date1, $date2)
    {

        return $this->users()->with([
            'invoices' => function ($query) use ($date1, $date2) {
                //$query->where('status', 1)
                $query->where('office_id', $this->id)
                    ->where([
                        ['invoices.created_at', '>=', $date1->startOfDay()],
                        ['invoices.created_at', '<=', $date2->endOfDay()]
                    ]);
            }
        ])->whereHas('roles', function ($query) {
            $query->where('name', 'medico')
                ->orWhere('name', 'clinica')
                ->orWhere('name', 'asistente');
        })->where('active', 1)->get();
    }

    public function medicsWithIncomes($date1, $date2)
    {


        return $this->users()->with([
            'incomes' => function ($query) use ($date1, $date2) {
                $query->where([
                    ['incomes.date', '>=', $date1->startOfDay()],
                    ['incomes.date', '<=', $date2->endOfDay()]
                ]);
            }
        ])->whereHas('roles', function ($query) {
            $query->where('name', 'medico');
        })->where('active', 1)->get();
    }

    public function administrator(array $roles = ['clinica', 'laboratorio'])
    {
        return $this->users()->whereHas('roles', function ($query) use ($roles) {
            $query->whereIn('name', $roles);
        })->where('active', 1)->first();
    }

    public function assistants()
    {
        return $this->users()->whereHas('roles', function ($query) {
            $query->where('name', 'asistente');
        })->where('active', 1)->get();
    }

    public function configFactura()
    {
        return $this->morphMany(ConfigFactura::class, 'facturable');
    }

    public function clinicsAssistants()
    {
        return $this->belongsToMany(User::class, 'assistants_offices', 'office_id', 'assistant_id');
    }

    public function hasMedic($medic)
    {
        if ($medic instanceof User) {
            $medic = $medic->id;
        }

        $medics = $this->doctors();

        return $medics->contains('id', $medic);
    }

    public function doctors($search = null)
    {

        if ($search) {

            $data = $this->users()->with('specialities')->whereHas('roles', function ($query) use ($search) {
                $query->where('name', 'medico');
            })->where('name', 'like', '%'.$search.'%');
        } else {
            $data = $this->users()->with('specialities')->whereHas('roles', function ($query) {
                $query->where('name', 'medico');
            });
        }

        $data = $data->with([
            'schedules' => function ($q) {
                $q->whereDate('date', '>=', Carbon::now()->startOfDay()->toDateTimeString())
                    ->take(7)->orderBy('date', 'ASC');
            }
        ])->withCount([
            'schedules' => function ($q) {
                $q->whereDate('date', '>=', Carbon::now()->startOfDay()->toDateTimeString())
                    ->take(7);
            }
        ])->orderBy('schedules_count', 'DESC');

        return $data->get();
    }

    public function patients()
    {
        return $this->belongsToMany(Patient::class);
    }

    public function centroMedicoPendingCharge()
    {
        $pending = [];

        $admins = $this->administrators();

        foreach ($admins as $admin) {
            if ($admin->monthlyCharge()->count()) {
                $pending[] = $admin->id;
            }
        }

        return count($pending) ? true : false;
    }

    public function administrators()
    {
        return $this->users()->whereHas('roles', function ($query) {
            $query->where('name', 'clinica')
                ->orWhere('name', 'laboratorio');
        })->where('active', 1)->get();
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    protected function name(): Attribute
    {
        return Attribute::make(
        //get: fn ($value) => strtoupper($value),
            set: fn($value) => strtoupper($value),
        );
    }

    /**
     * @param $units
     */
    private function distanceUnit($units = 'K')
    {
        if ($units == 'K') {
            return static::DISTANCE_UNIT_KILOMETERS;
        } elseif ($units == 'M') {
            return static::DISTANCE_UNIT_MILES;
        } else {
            throw new Exception("Unknown distance unit measure '$units'.");
        }
    }
}

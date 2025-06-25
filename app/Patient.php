<?php

namespace App;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class Patient extends Model
{
    use Notifiable;
    use HasFactory;

    protected $guarded = [
        'password', 'fullPhone', 'authorization', 'medic_to_assign', 'account', 'discount_id', 'invitation',
        'contact_name', 'contact_phone_number', 'contact_phone_country_code', 'fullname', 'age', 'fullWhatsappPhone',
        'provinceAddress'
    ];

    protected $appends = ['fullname', 'fullPhone', 'age', 'fullWhatsappPhone'];

    protected $casts = [
        'birth_date' => 'date'
    ];


    public function getAgeAttribute()
    {
        return $this->birth_date?->diff(Carbon::now())
            ->format('%y');
    }

    public function getProvinceAddressAttribute()
    {
        return $this->province ? collect([
            trans('utils.provincias.'.$this->province), $this->canton, $this->district
        ])->filter()->implode(', ') : '';
    }

    public function setConditionsAttribute($value)
    {
        $data = $value ? json_encode($value) : null;

        $this->attributes['conditions'] = $data;
    }

    public function getConditionsAttribute($value)
    {
        return $value ? json_decode($value) : null;
    }

    public function getAvatarPathAttribute($avatar)
    {
        if (!$avatar) {
            return asset('img/default-avatar.jpg');
        }


        return \Storage::disk('s3')->url($avatar);
    }

    public function getFullPhoneAttribute()
    {
        return ($this->phone_country_code ?? '+506').$this->phone_number;
    }

    public function getFullWhatsAppPhoneAttribute()
    {
        return ($this->phone_country_code ?? '+506').$this->whatsapp_number;
    }

    public function getFullnameAttribute()
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function scopeSearch($query, $search)
    {
        if (isset($search['q']) && $search['q']) {

            $query = $query->where(function ($query) use ($search) {
                $query->where('ide', 'like', '%'.$search['q'].'%')
                    //->orWhereRaw("concat(first_name, ' ', last_name) like '%{$search['q']}%' ")
                    ->orWhere('first_name', 'like', '%'.$search['q'].'%')
                    ->orWhere('phone_number', 'like', '%'.$search['q'].'%')
                    ->orWhere('conditions', 'like', '%'.$search['q'].'%');
            });
        }

        if (isset($search['province']) && $search['province']) {
            $query = $query->where('province', $search['province']);
        }

        if (isset($search['conditions']) && $search['conditions']) {
            $query = $query->where('conditions', 'like', '%'.$search['conditions'].'%');
        }


        return $query;
    }

    public function apipharmacredentials()
    {
        return $this->hasMany(Apipharmacredential::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function user()
    {
        return $this->belongsToMany(User::class)->using(PatientUser::class);
    }

    public function offices()
    {
        return $this->belongsToMany(Office::class);
    }

    public function pharmacies()
    {
        return $this->belongsToMany(Pharmacy::class);
    }

    public function authorizations()
    {
        return $this->belongsToMany(User::class)->wherePivot('authorization', 1);
    }

    public function medicines()
    {
        return $this->hasMany(Medicine::class);
    }

    public function allergies()
    {
        return $this->hasMany(Allergy::class, 'history_id'); // history_id is same as patient_id
    }

    public function archivos()
    {
        return $this->hasMany(Archivo::class);
    }

    public function labexams()
    {
        return $this->hasMany(Labexam::class);
    }

    public function labresults()
    {
        return $this->hasMany(Labresult::class);
    }

    public function pressures()
    {
        return $this->hasMany(Pressure::class);
    }

    public function sugars()
    {
        return $this->hasMany(Sugar::class);
    }

    public function discounts()
    {
        return $this->belongsToMany(Discount::class);
    }

    public function emergencyContacts()
    {
        return $this->hasMany(EmergencyContact::class);
    }

    public function pmedicines()
    {
        return $this->hasMany(Pmedicine::class);
    }

    public function vitalSigns()
    {
        return $this->hasMany(VitalSign::class);
    }

    public function createHistory($history = null)
    {
        $history = ($history) ? $history : new History();
        $history->histories = '';

        return $this->history()->save($history);
    }

    public function history()
    {
        return $this->hasOne(History::class);
    }

    /*public function ppressures()
    {
        return $this->hasMany(Ppressure::class);
    }

    public function psugars()
    {
        return $this->hasMany(Psugar::class);
    }
*/

    public function isPatientOf($user)
    {
        if ($user instanceof User) {
            $user = $user->id;
        }

        return $this->user->contains('id', $user);
    }

    public function isPatientAuthorizedOf($user)
    {
        if ($user instanceof User) {
            $user = $user->id;
        }

        return \DB::table('patient_user')
            ->where('user_id', $user)
            ->where('patient_id', $this->id)
            ->where('authorization', 1)->exists();
    }

    public function isPatientOfClinic($office)
    {
        if ($office instanceof Office) {
            $office = $office->id;
        }

        return $this->offices->contains('id', $office);
    }

    public function isPatientOfClinicAuthorizedOf($office)
    {
        if ($office instanceof Office) {
            $office = $office->id;
        }

        return \DB::table('office_patient')
            ->where('office_id', $office)
            ->where('patient_id', $this->id)
            ->where('authorization', 1)->exists();
    }

    public function isPatientOfPharmacy($pharmacy)
    {
        if ($pharmacy instanceof Pharmacy) {
            $pharmacy = $pharmacy->id;
        }

        return $this->pharmacies->contains('id', $pharmacy);
    }

    public function isPatientOfPharmacyAuthorizedOf($pharmacy)
    {
        if ($pharmacy instanceof Pharmacy) {
            $pharmacy = $pharmacy->id;
        }

        return \DB::table('patient_pharmacy')
            ->where('pharmacy_id', $pharmacy)
            ->where('patient_id', $this->id)
            ->where('authorization', 1)->exists();
    }

    /**
     * Determine if the user has the given role.
     *
     * @param  mixed  $role
     * @return boolean
     */
    public function hasDiscount($discount)
    {
        if ($discount instanceof Discount) {
            $discount = $discount->id;
        }

        return $this->discounts->contains('id', $discount);
    }

    public function accumulateds()
    {
        return $this->belongsToMany(Accumulated::class);
    }

    public function saveDoseReminders($items)
    {


        foreach ($items as $item) {

            $item = Arr::except($item, array('id', 'patient_id'));
            $this->dosereminders()->create($item);
        }

        return $this;
    }

    public function dosereminders()
    {
        return $this->hasMany(Dosereminder::class);
    }

    public function optreatments()
    {
        return $this->belongsToMany(Optreatment::class)->withTimestamps();
    }

    protected function firstName(): Attribute
    {
        return Attribute::make(
        //get: fn ($value) => strtoupper($value),
            set: fn($value) => strtoupper($value),
        );
    }

    protected function phoneNumber(): Attribute
    {
        return Attribute::make(
            set: fn($value) => ['whatsapp_number' => $value, 'phone_number' => $value]
        );
    }
}

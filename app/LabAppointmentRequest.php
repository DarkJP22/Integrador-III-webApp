<?php

namespace App;

use App\Enums\LabAppointmentRequestStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class LabAppointmentRequest extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $appends = ['provinceName'];

    protected $casts = [
        'appointment_date' => 'datetime',
        'status' => LabAppointmentRequestStatus::class,
    ];

    public function getProvinceNameAttribute()
    {
        return trans('utils.provincias.'.$this->province);
    }

    public function scopeSearch($query, $search)
    {
        if (isset($search['q']) && $search['q']) {

            $query = $query->where(function ($query) use ($search) {
                $query->where('patient_ide', 'like', '%'.$search['q'].'%')
                    ->orWhere('patient_name', 'like', '%'.$search['q'].'%')
                    ->orWhere('phone_number', 'like', '%'.$search['q'].'%')
                    ->orWhere('reference_ide', 'like', '%'.$search['q'].'%')
                    ->orWhere('reference_name', 'like', '%'.$search['q'].'%')
                    ->orWhereHas('user', function ($query) use ($search) {
                        $query->where('ide', 'like', '%'.$search['q'].'%');
                    });
            });
        }

        if (isset($search['start']) && $search['start']) {
            $start = new Carbon($search['start']);
            $end = (isset($search['end']) && $search['end'] != "") ? $search['end'] : $search['start'];
            $end = new Carbon($end);

            $query = $query->where([
                ['appointment_date', '>=', $start],
                ['appointment_date', '<=', $end->endOfDay()]
            ]);
        }


        return $query;
    }

    public function scopePending($query)
    {

        return $query->where('status', LabAppointmentRequestStatus::PENDING);


    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function office()
    {
        return $this->belongsTo(Office::class);
    }
}

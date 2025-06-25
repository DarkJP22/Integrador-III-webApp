<?php

namespace App;

use App\Enums\MedicineReminderStatus;
use App\Enums\MedicineReminderStatusNotification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class MedicineReminder extends Model
{
    public static $searchable = [
        'name',
        'date'
    ];
    protected $guarded = [];
    protected $table = 'medicine_reminders';
    protected $casts = [
        'date' => 'datetime',
        'status' => MedicineReminderStatus::class,
        'status_notification' => MedicineReminderStatusNotification::class,
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($notification) {
            \DB::table('medicine_reminder_patient')->where('medicine_reminder_id', $notification->id)->delete();
        });
    }

    public function scopeSearch($query, $search)
    {
        if (isset($search['q']) && $search['q']) {
            $query->where(function ($query) use ($search) {
                $query->Where('name', 'like', '%'.$search['q'].'%')
                    ->orWhereHas('patients', function ($query) use ($search) {
                        $query->where('first_name', 'like', '%'.$search['q'].'%')
                            ->orWhere('last_name', 'like', '%'.$search['q'].'%');

                    });
            });
        }

        if (isset($search['start']) && $search['start']) {
            $start = new Carbon($search['start']);
            $end = (isset($search['end']) && $search['end'] != "") ? $search['end'] : $search['start'];
            $end = new Carbon($end);

            $query = $query->where([
                ['date', '>=', $start],
                ['date', '<=', $end->endOfDay()]
            ]);
        }

        if (isset($search['status']) && $search['status']) {
            if(+$search['status'] === -1){
                return $query;
            }

            $query->where('status', $search['status']);
        }

        return $query;
    }

    public function patients()
    {
        return $this->belongsToMany(Patient::class);
    }

    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class);
    }

    public function medicine()
    {
        return $this->belongsTo(Pmedicine::class, 'medicine_id');
    }
}

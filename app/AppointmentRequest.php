<?php

namespace App;

use App\Enums\AppointmentRequestStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AppointmentRequest extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'status' => AppointmentRequestStatus::class,
        'scheduled_at' => 'datetime',
        'scheduled_date' => 'date',
        'start' => 'datetime',
        'end' => 'datetime',
        'date' => 'datetime',
    ];


    public function scopeSearch($query, $search)
    {
        if (isset($search['q']) && $search['q']) {

            $query = $query->whereHas('patient', function ($query) use ($search) {
                $query->where('first_name', 'like', '%'.$search['q'].'%')
                    ->orWhere('ide', 'like', '%'.$search['q'].'%');
            });

        }

        if (isset($search['start']) && $search['start']) {
            $start = new Carbon($search['start']);
            $end = (isset($search['end']) && $search['end'] != "") ? $search['end'] : $search['start'];
            $end = new Carbon($end);

            $query = $query->where([
                ['created_at', '>=', $start],
                ['created_at', '<=', $end->endOfDay()]
            ]);
        }

        return $query;
    }

    public function scopeByDiffInMinutes($query, $date1, $date2, $operator, $minutes)
    {
       return $query->when(DB::connection()->getDriverName() === 'sqlite', function ($query) use ($date1, $date2,$operator, $minutes) {
            $query->whereRaw("ABS(ROUND((julianday({$date2}) - julianday({$date1})) * 24 * 60)) {$operator} ?", $minutes);
        }, function ($query) use ($date1, $date2, $operator, $minutes) {
            $query->whereRaw("ABS(TIMESTAMPDIFF(MINUTE, {$date1}, {$date2})) {$operator} ?", $minutes);
        });
    }

    public function scopeByAverageResponse($query)
    {
        return $query->when(DB::connection()->getDriverName() === 'sqlite', function ($query) {
            $query->selectRaw('AVG(ROUND((julianday(scheduled_at) - julianday(created_at)) * 24 * 60)) as average_response');
        }, function ($query) {
            $query->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, scheduled_at)) as average_response');
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function medic()
    {
        return $this->belongsTo(User::class, 'medic_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function reminders()
    {
        return $this->morphMany(Reminder::class, 'resource');
    }
}

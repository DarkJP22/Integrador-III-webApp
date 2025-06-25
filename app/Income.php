<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class Income extends Model
{
    protected $fillable = [
        'date', 'amount', 'pending', 'paid', 'type', 'date', 'month', 'year', 'period_from', 'period_to', 'subscription_cost', 'medic_type', 'office_id', 'appointment_id', 'description', 'purchase_operation_number', 'voucher'
    ];
    protected $casts = [
        'date' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function scopeSearch($query, $search)
    {

        if (isset($search['start']) && $search['start']) {
            $start = new Carbon($search['start']);
            $end = (isset($search['end']) && $search['end'] != "") ? $search['end'] : $search['start'];
            $end = new Carbon($end);
           
            $query = $query->where([
                ['incomes.date', '>=', $start->startOfDay()],
                ['incomes.date', '<=', $end->endOfDay()]
            ]);
        }
       
        return $query;
    }


    public function medic()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}

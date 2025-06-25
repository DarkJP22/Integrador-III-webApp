<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Plan extends Model
{
    use HasFactory;

    protected $fillable  = ['title', 'description', 'cost', 'quantity', 'for_medic', 'for_clinic', 'for_pharmacy', 'for_lab', 'include_fe', 'include_assistant', 'commission_by_appointment', 'general_cost_commission_by_appointment', 'specialist_cost_commission_by_appointment', 'currency_id', 'commission_discount', 'commission_discount_range_in_minutes'];
    protected $appends = ['costName'];
    protected $casts =[
      'general_cost_commission_by_appointment' => 'float',
      'specialist_cost_commission_by_appointment' => 'float',
    ];
    public function getCostNameAttribute()
    {
        $defaultCurrency = getDefaultCurrency();
        return $this->cost > 0 ? (money($this->cost, $this->currency->symbol ?? $defaultCurrency->symbol) . ' / ' . $this->quantity . ' Mes(es)') : 'Gratis';
    }

    public function scopeSearch($query, $search)
    {
        if ($search) {

            return $query->where(function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('cost', 'like', '%' . $search . '%');
            });
        }


        return $query;
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
}

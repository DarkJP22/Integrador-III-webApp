<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabVisit extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $casts = [
        'schedule' => 'array',
    ];

    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function ($query) use ($search) {
                $query->where('location', 'like', '%' . $search . '%');
            });
        }

        return $query;
    }

    public function office()
    {
        return $this->belongsTo(Office::class);
    }


}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AffiliationUsers extends Model
{
    use HasFactory;
    protected $table = 'affiliation_users';

    protected $fillable = [
        'user_id',
        'date',
        'active',
        'type_affiliation',
        'voucher',
        "created_at",
        "updated_at",
        'discount'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

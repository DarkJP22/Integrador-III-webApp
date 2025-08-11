<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AffiliationUsers extends Model
{
    use HasFactory;
     public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'affiliation_users';

    protected $fillable = [
        'id',
        'user_id',
        'date',
        'active',
        'type_affiliation',
        'voucher',
        "created_at",
        "updated_at",
        'discount',
        'priceToAffiliation'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

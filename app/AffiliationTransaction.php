<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AffiliationTransaction extends Model
{
    protected $guarded = [];
    protected $table = 'affiliation_transactions';

    /**
     * Get the owning commentable model.
     */
    public function transactable()
    {
        return $this->morphTo();
    }

    public function affiliation()
    {
        return $this->belongsTo(Affiliation::class);
    }
}

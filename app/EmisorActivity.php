<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmisorActivity extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'emisor_activities';

    public function configFactura()
    {
        return $this->belongsTo(ConfigFactura::class);
    }
}

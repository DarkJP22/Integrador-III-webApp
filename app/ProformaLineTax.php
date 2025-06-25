<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProformaLineTax extends Model
{
    protected $guarded = [];
    protected $table = 'proforma_line_taxes';
    public $timestamps = false;

    public function proformaLine()
    {
        return $this->belongsTo(ProformaLine::class);
    }
}

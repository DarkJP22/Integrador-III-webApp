<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProformaLineDiscount extends Model
{
    use HasFactory;

    protected $guarded = ['NoEditable'];
    protected $table = 'proforma_line_discounts';
    public $timestamps = false;

    public function proformaLine()
    {
        return $this->belongsTo(ProformaLine::class);
    }
}

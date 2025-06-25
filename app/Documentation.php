<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Documentation extends Model
{
    use HasFactory;

    protected $guarded = ['file','file_path'];

    protected $appends = ['file_path'];

    public function getFilePathAttribute()
    {

        return $this->url ? \Storage::disk('s3')->url($this->url) : false;
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}

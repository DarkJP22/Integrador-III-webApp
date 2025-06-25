<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PhysicalExam extends Model
{
    protected $fillable = [
        'cardio','digestivo','urinario','linfatico','dermatologico','neurologico','osteoarticular','otorrinolaringológico','pulmonar','psiquiatrico','reproductor'
    ];
    protected $table = 'physical_exams';

     public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}

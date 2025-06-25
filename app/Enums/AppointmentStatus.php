<?php

namespace App\Enums;

enum AppointmentStatus: int
{
    case SCHEDULED = 0;
    case STARTED = 1;
    case CANCELLED = 2;
    case OTHER = 99;



    public function label(): string
    {
        return match($this) {
            static::SCHEDULED => __('Agendada'),
            static::STARTED => __('Iniciada'),
            static::CANCELLED => __('Cancelada'),
            static::OTHER => __('Otro'),
        };
    }
}

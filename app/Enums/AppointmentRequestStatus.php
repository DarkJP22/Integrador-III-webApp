<?php

namespace App\Enums;

enum AppointmentRequestStatus: int
{
    case RESERVED = 0;
    case SCHEDULED = 1;
    case PENDING = 2;
    case CANCELLED = 3;


    static public function options(): array
    {
        return [
            [ 'id' => static::RESERVED->value, 'name' => static::RESERVED->label()],
            [ 'id' => static::SCHEDULED->value, 'name' => static::SCHEDULED->label()],
            [ 'id' => static::PENDING->value, 'name' => static::PENDING->label()],
            [ 'id' => static::CANCELLED->value, 'name' => static::CANCELLED->label()],

        ];
    }
    public function label(): string
    {
        return match($this) {
            static::RESERVED => __('Reservada'),
            static::SCHEDULED => __('Agendada'),
            static::PENDING => __('Pendiente'),
            static::CANCELLED => __('Cancelada'),
        };
    }
}

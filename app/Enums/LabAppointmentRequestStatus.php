<?php

namespace App\Enums;

enum LabAppointmentRequestStatus: int
{
    case PENDING = 0;
    case COMPLETED = 1;


    static public function options(): array
    {
        return [
            [ 'id' => self::PENDING->value, 'name' => self::PENDING->label()],
            [ 'id' => self::COMPLETED->value, 'name' => self::COMPLETED->label()],


        ];
    }
    public function label(): string
    {
        return match($this) {
            self::PENDING => __('Pendiente'),
            self::COMPLETED => __('Completada'),
        };
    }
}

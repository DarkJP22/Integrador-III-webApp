<?php

namespace App\Enums;

enum OfficeType: int
{
    case MEDIC_OFFICE = 1;
    case CLINIC = 2;
    case LABORATORY = 3;


    static public function options(): array
    {
        return [
            [ 'id' => self::MEDIC_OFFICE->value, 'name' => self::MEDIC_OFFICE->label()],
            [ 'id' => self::CLINIC->value, 'name' => self::CLINIC->label()],
            [ 'id' => self::LABORATORY->value, 'name' => self::LABORATORY->label()],

        ];
    }

    public function label(): string
    {
        return match($this) {
            self::MEDIC_OFFICE => __('Consultorio Independiente'),
            self::CLINIC => __('Clínica Privada'),
            self::LABORATORY => __('Laboratorio'),

        };
    }

    static public function optionsAsConst(): array
    {
        return [
            'MEDIC_OFFICE' => self::MEDIC_OFFICE,
            'CLINIC' => self::CLINIC,
            'LABORATORY' => self::LABORATORY,


        ];
    }
}

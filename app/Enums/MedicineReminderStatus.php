<?php

namespace App\Enums;

enum MedicineReminderStatus: int
{
    case NO_CONTACTED = 1;
    case CONTACTED = 2;


    static public function options(): array
    {
        return [
            [ 'id' => self::CONTACTED->value, 'name' => self::CONTACTED->label()],
            [ 'id' => self::NO_CONTACTED->value, 'name' => self::NO_CONTACTED->label()],


        ];
    }

    public function label(): string
    {
        return match($this) {
            self::CONTACTED => __('Contactado'),
            self::NO_CONTACTED => __('No contactado'),

        };
    }

    static public function optionsAsConst(): array
    {
        return [
            'CONTACTED' => self::CONTACTED,
            'NO_CONTACTED' => self::NO_CONTACTED,
        ];
    }

    public function color(): string
    {
        return match($this) {
            self::CONTACTED => 'success',
            self::NO_CONTACTED => 'warning',

        };
    }
}

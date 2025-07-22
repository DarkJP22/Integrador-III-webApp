<?php

namespace App\Enums;

enum PaymentMethod: int
{
    case EFECTIVO = 0;
    case SINPE = 1;

    public function label(): string
    {
        return match($this) {
            static::EFECTIVO => __('Efectivo'),
            static::SINPE => __('SINPE'),
        };
    }

    public static function getOptions(): array
    {
        return [
            static::EFECTIVO->value => static::EFECTIVO->label(),
            static::SINPE->value => static::SINPE->label(),
        ];
    }

    public function isElectronic(): bool
    {
        return $this === static::SINPE;
    }
}

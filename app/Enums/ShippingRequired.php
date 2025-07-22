<?php

namespace App\Enums;

enum ShippingRequired: int
{
    case NO = 0;
    case YES = 1;

    public function label(): string
    {
        return match($this) {
            static::NO => __('No requiere envío'),
            static::YES => __('Requiere envío'),
        };
    }

    public static function getOptions(): array
    {
        return [
            static::NO->value => static::NO->label(),
            static::YES->value => static::YES->label(),
        ];
    }

    public function requiresShipping(): bool
    {
        return $this === static::YES;
    }
}

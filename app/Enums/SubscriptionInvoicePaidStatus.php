<?php

namespace App\Enums;

enum SubscriptionInvoicePaidStatus: int
{
    case UNPAID = 1;
    case CHECKING = 2;
    case PAID = 3;
    case REFUSED = 4;



    static public function options(): array
    {
        return [
            [ 'id' => static::UNPAID->value, 'name' => static::UNPAID->label()],
            [ 'id' => static::CHECKING->value, 'name' => static::CHECKING->label()],
            [ 'id' => static::PAID->value, 'name' => static::PAID->label()],
            [ 'id' => static::REFUSED->value, 'name' => static::REFUSED->label()],

        ];
    }

    public function label(): string
    {
        return match($this) {
            static::UNPAID => __('Pendiente'),
            static::CHECKING => __('En Revisión'),
            static::PAID => __('Pagada'),
            static::REFUSED => __('Rechazada'),
        };
    }

    static public function optionsAsConst(): array
    {
        return [
             'UNPAID' => static::UNPAID,
             'CHECKING' => static::CHECKING,
             'PAID' => static::PAID,
             'REFUSED' => static::REFUSED

        ];
    }
}

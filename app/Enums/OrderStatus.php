<?php

namespace App\Enums;

enum OrderStatus: string
{
    case COTIZACION = 'cotizacion';
    case ESPERANDO_CONFIRMACION = 'esperando_confirmacion';
    case CONFIRMADO = 'confirmado';
    case PREPARANDO = 'preparando';
    case CANCELADO = 'cancelado';
    case DESPACHADO = 'despachado';

    public function label(): string
    {
        return match($this) {
            static::COTIZACION => __('Cotización'),
            static::ESPERANDO_CONFIRMACION => __('Esperando Confirmación'),
            static::CONFIRMADO => __('Confirmado'),
            static::PREPARANDO => __('Preparando'),
            static::CANCELADO => __('Cancelado'),
            static::DESPACHADO => __('Despachado'),
        };
    }

    public static function getOptions(): array
    {
        return [
            static::COTIZACION->value => static::COTIZACION->label(),
            static::ESPERANDO_CONFIRMACION->value => static::ESPERANDO_CONFIRMACION->label(),
            static::CONFIRMADO->value => static::CONFIRMADO->label(),
            static::PREPARANDO->value => static::PREPARANDO->label(),
            static::CANCELADO->value => static::CANCELADO->label(),
            static::DESPACHADO->value => static::DESPACHADO->label(),
        ];
    }
}

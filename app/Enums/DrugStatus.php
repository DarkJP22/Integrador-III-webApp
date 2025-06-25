<?php

namespace App\Enums;

enum DrugStatus: int
{
    case PUBLISHED = 1;
    case UNPUBLISHED = 2;

    static public function options(): array
    {
        return [
            [ 'id' => self::PUBLISHED->value, 'name' => self::PUBLISHED->label()],
            [ 'id' => self::UNPUBLISHED->value, 'name' => self::UNPUBLISHED->label()],


        ];
    }

    public function label(): string
    {
        return match($this) {
            self::PUBLISHED => __('Publicado'),
            self::UNPUBLISHED => __('No Publicado'),


        };
    }

    static public function optionsAsConst(): array
    {
        return [
            'PUBLISHED' => self::PUBLISHED,
            'UNPUBLISHED' => self::UNPUBLISHED,



        ];
    }
}

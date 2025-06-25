<?php

namespace App\Enums;

enum TypeOfHealthProfessional : string
{
    case MEDICO = 'medico';
    case ODONTOLOGO = 'odontologo';
    case NUTRICIONISTA = 'nutricionista';
    case PSICOLOGO = 'psicologo';
    case FISIOTERAPEUTA = 'fisioterapeuta';

    static public function options(): array
    {
        return [
            [ 'id' => self::MEDICO->value, 'name' => self::MEDICO->label()],
            [ 'id' => self::ODONTOLOGO->value, 'name' => self::ODONTOLOGO->label()],
            [ 'id' => self::NUTRICIONISTA->value, 'name' => self::NUTRICIONISTA->label()],
            [ 'id' => self::PSICOLOGO->value, 'name' => self::PSICOLOGO->label()],
            [ 'id' => self::FISIOTERAPEUTA->value, 'name' => self::FISIOTERAPEUTA->label()],


        ];
    }

    public function label(): string
    {
        return match($this) {
            self::MEDICO => __('Médico'),
            self::ODONTOLOGO => __('Odontólogo'),
            self::NUTRICIONISTA => __('Nutricionista'),
            self::PSICOLOGO => __('Psicólogo'),
            self::FISIOTERAPEUTA => __('Fisioterapeuta'),


        };
    }

    static public function optionsAsConst(): array
    {
        return [
            'MEDICO' => self::MEDICO,
            'ODONTOLOGO' => self::ODONTOLOGO,
            'NUTRICIONISTA' => self::NUTRICIONISTA,
            'PSICOLOGO' => self::PSICOLOGO,
            'FISIOTERAPEUTA' => self::FISIOTERAPEUTA,



        ];
    }
}

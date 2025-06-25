<?php

namespace App\Enums;

enum MedicineReminderStatusNotification: int
{
    case NOT_SENT = 1;
    case SENT = 2;
    case RESPONSE_YES = 3;
    case RESPONSE_NO = 4;
    case DID_NOT_ANSWER = 5;


    static public function options(): array
    {
        return [
            [ 'id' => self::SENT->value, 'name' => self::SENT->label()],
            [ 'id' => self::NOT_SENT->value, 'name' => self::NOT_SENT->label()],
            [ 'id' => self::RESPONSE_YES->value, 'name' => self::RESPONSE_YES->label()],
            [ 'id' => self::RESPONSE_NO->value, 'name' => self::RESPONSE_NO->label()],
            [ 'id' => self::DID_NOT_ANSWER->value, 'name' => self::DID_NOT_ANSWER->label()],

        ];
    }

    public function label(): string
    {
        return match($this) {
            self::SENT => __('Enviado'),
            self::NOT_SENT => __('No enviado'),
            self::RESPONSE_YES => __('Si lo quiero'),
            self::RESPONSE_NO => __('No lo quiero'),
            self::DID_NOT_ANSWER => __('No respondió'),
        };
    }

    static public function optionsAsConst(): array
    {
        return [
            'SENT' => self::SENT,
            'NOT_SENT' => self::NOT_SENT,
            'RESPONSE_YES' => self::RESPONSE_YES,
            'RESPONSE_NO' => self::RESPONSE_NO,
            'DID_NOT_ANSWER' => self::DID_NOT_ANSWER,
        ];
    }

    public function color(): string
    {
        return match($this) {
            self::SENT => 'success',
            self::NOT_SENT => 'warning',
            self::RESPONSE_YES => 'primary',
            self::RESPONSE_NO => 'danger',
            self::DID_NOT_ANSWER => 'default',
        };
    }
}

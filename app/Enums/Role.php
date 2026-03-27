<?php

namespace App\Enums;

enum Role: string
{
    case Admin = 'admin';
    case Patient = 'patient';
    case Dentist = 'dentist';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrator',
            self::Patient => 'Patient',
            self::Dentist => 'Dentist',
        };
    }
}
